<?php
require "core/GlobalHandler.php";
$global = new GlobalHandler();
$template = $global->template;
$database = $global->db;
$functions = $global->functions;

// Template Variable Initialization
$template->vars['{pageName}'] = "Add an Author";
$template->vars['{content}'] = $template->loadTemplate("authors/authors.add");
$template->vars['{notif}'] = "";

if(isset($_POST['addBtn'])) {
    $firstName = $_POST['authorFirst'];
    $lastName = $_POST['authorLast'];

    if(empty($firstName)) {
        $template->vars['{notif}'] .= $functions->generateAlert("Please enter a first name." ,0);
    } else if(empty($lastName)) {
        $template->vars['{notif}'] .= $functions->generateAlert("Please enter a last name." ,0);
    } else {
        $authorName = $firstName . ' ' . $lastName;
        // Insert Book Values
        $database->executeQuery("INSERT INTO Authors (AuthorName) VALUES ('{$authorName}')");
        $lastInsertID = $database->db->insert_id;
        header('Location: authors-view.php?id=' . $lastInsertID);
    }
}

$template->content();
?>
