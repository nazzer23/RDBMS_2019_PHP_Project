<?php
require "core/GlobalHandler.php";
$global = new GlobalHandler(false);
$template = new Template();
$database = $global->db;

// Template Variable Initialization
$template->vars['{siteName}'] = Configuration::siteName;
$template->vars['{pageName}'] = "Login";
$template->vars['{loginMsg}'] = "Sign in to start your session";

if (isset($_POST['username']) && isset($_POST['password'])) { 
    // This is terrible practice and shouldn't be used in any production application.
    // Please refer to one of my other PHP Repos to see one of my other login systems.
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Temp database connection to see if true
    $con = @new mysqli(Configuration::dbHost, $username, $password, Configuration::dbName);
    if ($con->connect_errno != 0) {
        $template->vars['{loginMsg}'] = "The username and password combination you entered was incorrect.";
        //die();
    } else {
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        header('Location: index.php');
    }
}

$template->template = $template->loadTemplate("login");

$template->content();
?>
