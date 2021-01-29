<?php
// Session Management
session_start();

// Imports
require('config.php');
require('handlers/DatabaseHandler.php');
require('handlers/TemplateHandler.php');
require('handlers/Functions.php');

class Main
{

    public $template;
    public $db;
    public $functions;

    /**
     * Main constructor.
     */
    public function __construct()
    {
        // Initialize Database
        $this->db = new DatabaseHandler();
        $this->preventInjection();

        // Initialize Functions
        $this->functions = new Functions($this);

        // Initialize Template System
        $this->template = new TemplateHandler("site.design");
        $this->setDefaultTemplateSettings();

        // Initialize Session Stuff
        $this->initializeNavbar();
    }

    /**
     *
     */
    private function preventInjection()
    {
        $_POST = $this->db->escapeArray($_POST);
        $_GET = $this->db->escapeArray($_GET);
    }

    /**
     *
     */
    private function setDefaultTemplateSettings()
    {
        $this->template->setVariable("author", Configuration::siteName);
        $this->template->setVariable("currentYear", date("Y"));
        $this->template->setVariable("siteName", Configuration::siteName);
    }

    /**
     *
     */
    public function initializeNavbar()
    {
        $this->template->setVariable("loggedInNav", "");
        if (!isset($_SESSION['logged'])) {
            $this->template->appendVariable("loggedInNav", '<li class="nav-item"><a class="nav-link" href="/?login">Login</a></li>'); // Login
            $this->template->appendVariable("loggedInNav", '<li class="nav-item"><a class="nav-link" href="/?signup">Register</a></li>'); // Register
        } else {
            $this->template->appendVariable("loggedInNav", '<li class="nav-item"><a class="nav-link" href="/?logout">Logout</a></li>'); // Logout
            $this->template->appendVariable("loggedInNav", '<li class="nav-item"><a class="nav-link" href="/?cart">Cart('.sizeof($_SESSION['cart']).')</a></li>'); // Cart
        }

    }

}

?>