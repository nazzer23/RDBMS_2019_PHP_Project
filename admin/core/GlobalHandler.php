<?php
session_start();
require __DIR__ . "/../../config.php";
require "handlers/Template.php";
require "handlers/Database.php";
require "handlers/Functions.php";

class GlobalHandler
{
    public $db;
    public $functions;
    public $template;
    public $userData;

    public function __construct($useTemplate = true)
    {
        $fileName = basename($_SERVER['PHP_SELF']);

        $userName = Configuration::dbUser;
        $password = Configuration::dbPass;

        if ($fileName != "login.php") {
            if (isset($_SESSION)) {
                if (!isset($_SESSION['username'])) {
                    header('Location: login.php');
                }
                $userName = $_SESSION['username'];
                $password = $_SESSION['password'];
            } else {
                header('Location: login.php');
            }
        }

        $this->db = new Database($userName, $password);

        $_GET = $this->db->escapeArray($_GET);
        $_POST = $this->db->escapeArray($_POST);

        if ($useTemplate) {
            $this->template = new Template();
            $this->template->vars['{siteName}'] = Configuration::siteName;
            $this->template->vars['{shortName}'] = "<strong>" . Configuration::siteName[0] . "</strong>" . substr(Configuration::siteName, -2);
            $this->template->vars['{Username}'] = $_SESSION['username'];
            $this->template->vars['{footerJS}'] = "";
            $this->template->vars['{currentYear}'] = date('Y');
            $this->template->vars['{currentVersion}'] = Configuration::adminVersion;
            $this->template->vars['{contentNav}'] = "";

            $this->authorInit();
        }
        $this->functions = new Functions($this);
    }

    function authorInit()
    {
        $temp = "";
        $temp .= '<li><a href="authors-add.php"><i class="fa fa-circle-o"></i> Add an Author</a></li>';
        $temp .= '<li><a href="authors.php"><i class="fa fa-circle-o"></i> View Authors</a></li>';
        $temp .= '<li><a href="customers.php"><i class="fa fa-circle-o"></i> View Customers</a></li>';
        $temp .= '<li><a href="book-add.php"><i class="fa fa-circle-o"></i> Add a Book</a></li>';
        $this->template->vars['{contentNav}'] .= $temp;
    }
}

?>