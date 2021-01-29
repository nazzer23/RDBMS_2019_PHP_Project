<?php
if (!isset($_GET['id'])) {
    header('Location: authors.php');
} else {

    require "core/GlobalHandler.php";
    $global = new GlobalHandler();
    $template = $global->template;
    $database = $global->db;

    // CHeck to see if Author ID is valid
    if ($database->getNumberOfRows("SELECT * FROM authors WHERE AuthorID='{$_GET['id']}'") <= 0) {
        header('Location: authors.php');
    } else {
        $authorData = $database->fetchArray("SELECT * FROM authors WHERE AuthorID='{$_GET['id']}'");
    }

// Template Variable Initialization
    $template->vars['{pageName}'] = $authorData['AuthorName'];
    $template->vars['{content}'] = $template->loadTemplate("authors/authors.view");

    // Profile
    $template->vars['{profileName}'] = $authorData['AuthorName'];
    $template->vars['{bookCount}'] = $database->getNumberOfRows("SELECT * FROM books WHERE AuthorID='{$_GET['id']}'");
    $template->vars['{bookSoldCount}'] = $database->getNumberOfRows("SELECT * FROM sales INNER JOIN books ON sales.BookID = books.BookID WHERE AuthorID='{$_GET['id']}'");

    // Books
    $template->vars['{authorsBooks}'] = null;
    $query = $database->executeQuery("SELECT * FROM books WHERE AuthorID ='{$authorData['AuthorID']}'");
    while($row = $query->fetch_array()) {
        if($row['Hidden']) {
            $updateLink = "<a href='book-view.php?id={$row['BookID']}'>Update</a>";
            $updateLink .= " <a href='book-view.php?id={$row['BookID']}&mode=unhide'>Unhide</a>";
        } else {
            $updateLink = "<a href='book-view.php?id={$row['BookID']}'>Update</a>";
        }

        $template->vars['{authorsBooks}'] .= "<tr>
                                                <td>{$row['BookID']}</td>
                                                <td>{$row['BookName']}</td>
                                                <td>{$row['BookISBN']}</td>
                                                <td>£{$row['BookPrice']}</td>
                                                <td>{$row['QuantityRemain']}</td>
                                                <td>{$updateLink}</td>
                                            </tr>";
    }

    $template->vars['{footerJS}'] .= "<script>$(document).ready(function () {
                                        $('#authorBooks').DataTable();
                                      });</script>";

    $template->content();

}