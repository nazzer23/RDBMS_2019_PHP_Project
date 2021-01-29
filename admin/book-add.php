<?php
require "core/GlobalHandler.php";
$global = new GlobalHandler();
$template = $global->template;
$database = $global->db;
$functions = $global->functions;

// Template Variable Initialization
$template->vars['{pageName}'] = "Add a Book";
$template->vars['{content}'] = $template->loadTemplate("books/book.add");
$template->vars['{notif}'] = "";
$template->vars['{authors}'] = "";

if(isset($_POST['addBtn'])) {
    $bookName = $_POST['bookName'];
    $bookPrice = $_POST['bookPrice'];
    $bookAuthor = $_POST['selectBookAuthor'];
    $bookISBN = $_POST['bookISBN'];
    $bookDesc = $_POST['bookBio'];

    if(empty($bookName)) {
        $template->vars['{notif}'] .= $functions->generateAlert("Please enter a book name." ,0);
    } else if(empty($bookPrice)) {
        $template->vars['{notif}'] .= $functions->generateAlert("Please enter a book price." ,0);
    } else if(empty($bookISBN)) {
        $template->vars['{notif}'] .= $functions->generateAlert("Please enter a book isbn." ,0);
    } else if(!is_numeric($bookPrice)) {
        $template->vars['{notif}'] .= $functions->generateAlert("Please enter a valid value for book price." ,0);
    } else if(empty($_FILES)) {
        $template->vars['{notif}'] .= $functions->generateAlert("Please upload a file." ,0);
    } else {
        // File Parameters
        $target_dir = "../images/books/";
        $target_file = $target_dir . $bookISBN.".jpg";
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $uploadOk = 1;

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            $uploadOk = 0;
        }

        if($uploadOk) {
            if (move_uploaded_file($_FILES["bookImageFile"]["tmp_name"], $target_file)) {
                // Insert Book Values
                $database->executeQuery("INSERT INTO books (BookName, BookPrice, AuthorID, BookDescription, BookISBN) VALUES ('{$bookName}', '{$bookPrice}', '{$bookAuthor}', '{$bookDesc}', '{$bookISBN}')");
                $lastInsertID = $database->db->insert_id;
                header('Location: /?book&id=' . $lastInsertID);
            } else {
                $uploadOk = 0;
            }
        }

        $template->vars['{notif}'] .= $functions->generateAlert("There was an error whilst uploading your image file." ,0);
    }
}


$authorQuery = "SELECT * FROM authors";
$authorQuery = $database->executeQuery($authorQuery);
while($row = $authorQuery->fetch_array()) {
    $template->vars['{authors}'] .= "<option value='{$row['AuthorID']}'>{$row['AuthorName']}</option>";
}

$template->content();
?>
