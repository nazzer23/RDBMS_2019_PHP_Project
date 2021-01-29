<?php
if (!isset($_GET['id'])) {
    header('Location: authors.php');
} else {

    require "core/GlobalHandler.php";
    $global = new GlobalHandler();
    $functions = $global->functions;
    $template = $global->template;
    $database = $global->db;

    $template->vars['{notif}'] = "";
    $template->vars['{authors}'] = "";

    $query = "SELECT * FROM books WHERE BookID='{$_GET['id']}'";

    // CHeck to see if Book ID is valid
    if ($database->getNumberOfRows($query) <= 0 && ($_GET['mode'] != "unhide")) {
        header('Location: authors.php');
    } else {
        $query = "SELECT * FROM books WHERE BookID='{$_GET['id']}'";
        $bookData = $database->fetchArray($query);

        if((isset($_GET['mode']) && $_GET['mode'] == "unhide") || (isset($_POST['unhideBtn']))) {
            $database->executeQuery("UPDATE books SET Hidden = 0 WHERE BookID = '{$_GET['id']}'");
            $template->vars['{notif}'] .= $functions->generateAlert("This book is now visible to the public.", 1);
        }

        // "Deletes" Book
        if(isset($_POST['deleteBtn'])) {
            $database->executeQuery("UPDATE books SET Hidden = 1 WHERE BookID = '{$_GET['id']}'");
            $template->vars['{notif}'] .= $functions->generateAlert("This book is now hidden to the public.", 1);
        }

        // Settings Pane
        if(isset($_POST['settingsSubmitBtn'])) {
            $inputAuthor = $_POST['selectBookAuthor'];
            $bookPrice = $_POST['bookPrice'];
            $bookBio = $_POST['bookBio'];

            if(!is_numeric($bookPrice)) {
                $template->vars['{notif}'] .= $functions->generateAlert("Please enter a valid price for this book.", 0);
            } else {
                $database->executeQuery("UPDATE books SET AuthorID={$inputAuthor}, BookPrice={$bookPrice}, BookDescription='{$bookBio}' WHERE BookID='{$_GET['id']}'");
                $template->vars['{notif}'] .= $functions->generateAlert("Book details have successfully been updated.", 1);
            }
        }

        // Restocks Book
        if(isset($_POST['restockBtn'])) {
            $restockAmount = $_POST['restockAmt'];
            if(!is_numeric($restockAmount)) {
                $template->vars['{notif}'] .= $functions->generateAlert("The value entered for restock was invalid.", 0);
            } else {
                $restockAmount += $bookData['QuantityRemain'];
                $database->executeQuery("UPDATE books SET QuantityRemain = {$restockAmount} WHERE BookID='{$_GET['id']}'");
                $template->vars['{notif}'] .= $functions->generateAlert("This book now has " . $restockAmount . " books in stock.", 1);
            }
        }

        $bookData = $database->fetchArray($query);
    }

    if($bookData['Hidden'] == 1) {
        $template->vars['{notif}'] .= $functions->generateAlert("The current book is hidden", 2);
    }
    
    $authorQuery = "SELECT * FROM authors";
    $authorQuery = $database->executeQuery($authorQuery);
    while($row = $authorQuery->fetch_array()) {
        $additional = "";
        if($row['AuthorID'] == $bookData['AuthorID']) {
            $additional = 'selected';
        }
        $template->vars['{authors}'] .= "<option value='{$row['AuthorID']}' {$additional}>{$row['AuthorName']}</option>";
    }

    // Profile
    $template->vars['{bookName}'] = $bookData['BookName'];
    $template->vars['{bookImage}'] = "/images/books/{$bookData['BookISBN']}.jpg";
    $template->vars['{bookISBN}'] = $bookData['BookISBN'];
    $template->vars['{bookStock}'] = $bookData['QuantityRemain'];
    $template->vars['{bookSoldCount}'] = $database->getNumberOfRows("SELECT * FROM sales INNER JOIN books ON sales.BookID = books.BookID WHERE books.BookID='{$_GET['id']}'");
    $template->vars['{bookPrice}'] = $bookData['BookPrice'];
    $template->vars['{bookBio}'] = $bookData['BookDescription'];

    // Master Vars
    $template->vars['{content}'] = $template->loadTemplate("books/book.view");
    $template->vars['{pageName}'] = $bookData['BookName'];

    $template->content();

}