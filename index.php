<?php
require('core/Main.php');
$main = new Main();
$mainTemplate = $main->template;
$database = $main->db;
$functions = $main->functions;

$themeFile = "";
switch (key($_GET)) {
    case "search":
        $themeFile = "site.search";
        break;
    case "book":
        $themeFile = "site.book";
        break;
    case "signup":
        if(isset($_SESSION['logged'])) {header('Location: /'); return;}
        $themeFile = "site.register";
        break;
    case "login":
        if(isset($_SESSION['logged'])) {header('Location: /'); return;}
        $themeFile = "site.login";
        break;
    case "logout":
        if(!isset($_SESSION['logged'])) {header('Location: /'); return;}
        session_destroy();
        header('Location: /');
        break;
    case "cart":
        if(!isset($_SESSION['logged'])) {header('Location: /?login'); return;}
        $themeFile = "site.cart";
        break;
    case "404":
        $themeFile = "site.404";
        break;
    default:
        $themeFile = "site.home";
        break;
}

// Global Page Handler
if(Configuration::siteLockdown) {
    $page = new TemplateHandler("site.maintenance");
    $page->setVariable("siteName", Configuration::siteName);

    $mainTemplate->setVariable("content", $page->getTemplate());
    $mainTemplate->render();
    return;
}

$page = new TemplateHandler($themeFile);
$page->setVariable("siteName", Configuration::siteName);

// Home Page
if ($themeFile == "site.home") {
    // Books (Show 3)
    $query = "SELECT * FROM books WHERE Hidden = 0 ORDER BY BookID DESC LIMIT 3";
    $query = $database->executeQuery($query);
    if ($query->num_rows > 0) {
        while ($rows = $query->fetch_object()) {
            $page->appendVariable("recommendedBooks", $functions->generateBookCard($rows));
        }
    } else {
        $page->setVariable("recommendedBooks", "There are no books added within the database yet. Please come back later!");
    }
} else
// Search
    if ($themeFile == "site.search") {
        $searchTerm = "";
        if (!isset($_POST['search']) || empty($_POST['search'])) {
            $cardBody = $functions->generateCardBody("No results were found.", "There were no results found for that search request.");
            $results = $functions->generateCard($cardBody, null, true);
        } else {
            $results = "";

            $searchTerm = $_POST['search'];

            $query = "SELECT * FROM books WHERE BookName LIKE '%{$_POST['search']}%' AND Hidden = 0 ORDER BY BookID ASC";
            $query = $database->executeQuery($query);
            if ($query->num_rows > 0) {
                while ($row = $query->fetch_object()) {
                    $results .= $functions->generateSearchCard($row);
                }
            } else {
                $cardBody = $functions->generateCardBody("No results were found.", "There were no results found for that search request.");
                $results = $functions->generateCard($cardBody, null, true);
            }
        }
        $page->setVariable("searchTerm", $searchTerm);
        $page->setVariable("searchResult", $results);
    } else
// Book Info
        if ($themeFile == "site.book") {
            if (isset($_GET['id'])) {
                $getBookInfo = "SELECT * FROM books WHERE BookID='{$_GET['id']}' AND Hidden = 0";
                $getBookInfo = $database->executeQuery($getBookInfo);
                if ($getBookInfo->num_rows <= 0) {
                    header('Location: /');
                } else {
                    $bookData = $getBookInfo->fetch_object();

                    if(isset($_GET['addToCart'])) {
                        if(!isset($_SESSION['logged'])) {
                            header('Location: /?login');
                        } else {
                            if($bookData->QuantityRemain > 0) {
                                if (!(in_array($_GET['id'], $_SESSION['cart']))) {
                                    $arrayIndex = sizeof($_SESSION['cart']);
                                    $_SESSION['cart'][$arrayIndex] = $_GET['id'];
                                    header('Location: /?cart');
                                }
                            }
                        }
                    }
                    // Book Header
                    $page->setVariable("bookName", $bookData->BookName);
                    $page->setVariable("authorName", $bookData->AuthorID == 0 ? "Unknown" : $functions->getAuthorByID($bookData->AuthorID));

                    // Middle Components
                    $page->setVariable("bookImage", "/images/books/" . $bookData->BookISBN . ".jpg");
                    $page->setVariable("bookData", $bookData->BookDescription == "" ? "There isn't a description for this book just yet." : nl2br($bookData->BookDescription));

                    // Book Details
                    $page->setVariable("isbn", $bookData->BookISBN);
                    $page->setVariable("price", $bookData->BookPrice);

                    // Stock Details
                    $stock = "";
                    $isDisabled = false;
                    if ($bookData->QuantityRemain == 0) {
                        $stock = "Out of Stock";
                        $isDisabled = true;
                    } else
                        if ($bookData->QuantityRemain < 10) {
                            $stock = "Limited Quantity Remaining";
                        } else {
                            $stock = $bookData->QuantityRemain;
                        }
                    $page->setVariable("stock", $stock);

                    if ($isDisabled) {
                        $page->setVariable("cartButton", '<button type="button" class="btn btn-danger btn-sm">This Product is currently unavailable</button>');
                    } else {
                        $page->setVariable("cartButton", '<a href="/?book&id=' . $bookData->BookID . '&addToCart=true" class="btn btn-primary btn-sm">Add to Cart</a>');
                    }

                    // More by this author
                    $query = "SELECT * FROM books WHERE AuthorID={$bookData->AuthorID} AND BookID !={$bookData->BookID} AND Hidden = 0 ORDER BY RAND() LIMIT 2";
                    $query = $database->executeQuery($query);
                    if ($query->num_rows > 0) {
                        while ($row = $query->fetch_object()) {
                            $page->appendVariable("authorBooks", $functions->generateBookCard($row));
                        }
                    } else {
                        $page->setVariable("authorBooks", '<p>We don\'t sell more books by this author.</p>');
                    }
                }
            } else {
                header('Location: /');
            }
        } // User Registration
        else if ($themeFile == "site.register") {
            $page->setVariable("notif", "");
            if (isset($_POST['strEmail']) && isset($_POST['strPassword']) && isset($_POST['firstName']) && isset($_POST['lastName'])) {
                $email = $_POST['strEmail'];
                $password = $_POST['strPassword'];
                $firstName = $_POST['firstName'];
                $lastName = $_POST['lastName'];
                if(empty($email)) {
                    $page->appendVariable("notif", generateNotif("Please enter an email address.", "danger"));
                } else if(empty($password)) {
                    $page->appendVariable("notif", generateNotif("Please enter a password.", "danger"));
                } else if(empty($firstName)) {
                    $page->appendVariable("notif", generateNotif("Please enter your first name.", "danger"));
                } else if(empty($lastName)) {
                    $page->appendVariable("notif", generateNotif("Please enter your last name.", "danger"));
                } else {
                    if($database->getNumberOfRows("SELECT * FROM customers WHERE Email LIKE '{$email}'") > 0) {
                        $page->appendVariable("notif", generateNotif("The email that was provided is already in use.", "danger"));
                    } else {
                        $database->executeQuery("INSERT INTO customers(FirstName, LastName, Email, Password) VALUES('{$firstName}', '{$lastName}', '{$email}', '{$password}')");
                        header('Location: /?login');
                    }
                }
            }
        } // User Login
        else if ($themeFile == "site.login") {
            $page->setVariable("notif", "");
            if (isset($_POST['strEmail']) && isset($_POST['strPassword'])) {
                $email = $_POST['strEmail'];
                $password = $_POST['strPassword'];
                if(empty($email)) {
                    $page->appendVariable("notif", generateNotif("Please enter your email address.", "danger"));
                } else if(empty($password)) {
                    $page->appendVariable("notif", generateNotif("Please enter your password.", "danger"));
                } else {
                    $query = "SELECT * FROM customers WHERE Email LIKE '{$email}' AND Password='{$password}'";
                    if($database->getNumberOfRows($query) <= 0) {
                        $page->appendVariable("notif", generateNotif("The email and password combination you entered was incorrect.", "danger"));
                    } else {
                        $userData = $database->db->query($query)->fetch_array();
                        $_SESSION['logged'] = $userData['CustomerID'];
                        $_SESSION['cart'] = array();
                        header('Location: /');
                    }
                }
            }
        } // Shopping Cart
        else if ($themeFile == "site.cart") {
            if(isset($_POST['checkoutBtn'])) {
                $localArr = $_SESSION['cart'];
                $_SESSION['cart'] = array();
                for($i = 0; $i < sizeof($localArr); $i++) {
                    $var = $localArr[$i];
                    $database->executeQuery("INSERT INTO sales (CustomerID, BookID) VALUES ('{$_SESSION['logged']}', '{$var}')");
                    $database->executeQuery("UPDATE books SET QuantityRemain = QuantityRemain - 1 WHERE BookID='{$var}'");
                }
            }

            if(isset($_GET['remove'])) {
                if (($key = array_search($_GET['remove'], $_SESSION['cart'])) !== false) {
                    unset($_SESSION['cart'][$key]);
                    $_SESSION['cart'] = array_values($_SESSION['cart']);
                }
            }

            $main->initializeNavbar();

            if(sizeof($_SESSION['cart']) == 0) {
                $page->setVariable("cartData", "<div class='text-center'>Your cart is empty.</div>");
            } else {
                $struct = "";
                $struct .= "<table class='table'>";
                $struct .= "<thead>";
                $struct .= "<tr>";
                $struct .= '<th>Book Name</th>';
                $struct .= "<th>Book ISBN</th>";
                $struct .= "<th>Book Price</th>";
                $struct .= "<th></th>";
                $struct .= "</tr>";
                $struct .= "</thead>";
                $struct .= "</tbody>";

                $totalPrice = 0.00;
                // For Loop
                for($i = 0; $i < sizeof($_SESSION['cart']); $i++) {
                    $bookID = $_SESSION['cart'][$i];
                    $bookQuery = $database->executeQuery("SELECT * FROM books WHERE BookID='{$bookID}'");
                    $struct .= "<tr>";
                    while($row = $bookQuery->fetch_array()) {
                        $totalPrice += $row['BookPrice'];
                        $struct .= "<td>{$row['BookName']}</td>";
                        $struct .= "<td>{$row['BookISBN']}</td>";
                        $struct .= "<td>£{$row['BookPrice']}</td>";
                        $struct .= "<td><a href='/?cart&remove={$row['BookID']}'>Remove</a></td>";
                    }
                    $struct .= "</tr>";
                }

                $struct .= "<tr><td></td><td></td><td>Total</td><td>£{$totalPrice}</td></tr>";
                $struct .= '<tr><td></td><td></td><td></td><td><form method="post">
                                                                    <button name="checkoutBtn" type="submit" class="btn btn-primary">
                                                                        Checkout
                                                                    </button>
                                                                </form></td></tr>';

                $struct .= "</tbody>";
                $struct .= "</table>";
                $page->setVariable("cartData", $struct);

            }
        }

$mainTemplate->setVariable("content", $page->getTemplate());
$mainTemplate->render();

function generateNotif($msg, $type) {
    return '<div class="alert alert-'.$type.'" role="alert">'.$msg.'</div>';
}
?>