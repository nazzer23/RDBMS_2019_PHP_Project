<?php
require "core/GlobalHandler.php";
$global = new GlobalHandler();
$template = $global->template;
$database = $global->db;

// Template Variable Initialization
$template->vars['{pageName}'] = "All Authors";
$template->vars['{content}'] = $template->loadTemplate("authors/authors.list");

$template->vars['{authors}'] = null;

$query = $database->executeQuery("SELECT * FROM authors");
while ($row = $query->fetch_array()) {
    $bookCount = $database->getNumberOfRows("SELECT * FROM books WHERE AuthorID = '{$row['AuthorID']}'");
    
    $template->vars['{authors}'] .= ("<tr>
                                        <td>{$row['AuthorID']}</td>
                                        <td>{$row['AuthorName']}</td>
                                        <td>{$bookCount}</td>
                                        <td><a href='authors-view.php?id={$row['AuthorID']}'>View Author</a></td>
                                    </tr>");
}

$template->vars['{footerJS}'] .= "<script>$(document).ready( function () {
    $('#tableOfAuthors').DataTable();
} );</script>";

$template->content();
?>
