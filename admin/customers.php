<?php
require "core/GlobalHandler.php";
$global = new GlobalHandler();
$template = $global->template;
$database = $global->db;

// Template Variable Initialization
$template->vars['{pageName}'] = "All Customers";
$template->vars['{content}'] = $template->loadTemplate("customers/customers.list");

$template->vars['{customers}'] = null;

$query = $database->executeQuery("SELECT * FROM customers");
while ($row = $query->fetch_array()) {

    $template->vars['{customers}'] .= ("<tr>
                                            <td>{$row['CustomerID']}</td>
                                            <td>{$row['FirstName']} {$row['LastName']}</td>
                                            <td>{$row['Email']}</td>
                                            <td><a href='customers-view.php?id={$row['CustomerID']}'>View Customer</a></td>
                                        </tr>");
}

$template->vars['{footerJS}'] .= "<script>$(document).ready( function () {
    $('#tableOfCustomers').DataTable();
} );</script>";

$template->content();
?>
