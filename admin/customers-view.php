<?php
if (!isset($_GET['id'])) {
    header('Location: customers.php');
} else {

    require "core/GlobalHandler.php";
    $global = new GlobalHandler();
    $template = $global->template;
    $database = $global->db;

    // CHeck to see if Author ID is valid
    if ($database->getNumberOfRows("SELECT * FROM customers WHERE CustomerID='{$_GET['id']}'") <= 0) {
        header('Location: customers.php');
    } else {
        $authorData = $database->fetchArray("SELECT * FROM customers WHERE CustomerID='{$_GET['id']}'");
    }

// Template Variable Initialization
    $template->vars['{pageName}'] = $authorData['FirstName'] . " " . $authorData['LastName'];
    $template->vars['{content}'] = $template->loadTemplate("customers/customers.view");

    // Profile
    $template->vars['{profileName}'] = $template->vars['{pageName}'];
    $template->vars['{bookCount}'] = $database->getNumberOfRows("SELECT * FROM sales WHERE CustomerID='{$_GET['id']}'");

    // Books
    $template->vars['{sales}'] = null;
    $query = $database->executeQuery("SELECT * FROM sales INNER JOIN books ON books.BookID=sales.BookID WHERE CustomerID='{$_GET['id']}'");
    while($row = $query->fetch_array()) {
        $template->vars['{sales}'] .= "<tr>
                                                <td>{$row['SaleID']}</td>
                                                <td>{$row['BookName']}</td>
                                                <td>£{$row['BookPrice']}</td>
                                                <td>{$row['DatePurchased']}</td>
                                            </tr>";
    }

    $template->vars['{footerJS}'] .= "<script>$(document).ready(function () {
                                        $('#sales').DataTable();
                                      });</script>";

    $template->content();

}