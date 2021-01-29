<?php
require "core/GlobalHandler.php";
$global = new GlobalHandler();
$template = $global->template;
$database = $global->db;
$functions = $global->functions;

// Template Variable Initialization
$template->vars['{pageName}'] = "Dashboard";
$template->vars['{content}'] = $template->loadTemplate("home");
$template->vars['{charts}'] = null;

// Top Bar Values
$template->vars['{bookCount}'] = $database->getNumberOfRows("SELECT * FROM books");
$template->vars['{authorCount}'] = $database->getNumberOfRows("SELECT * FROM authors");
$template->vars['{customerCount}'] = $database->getNumberOfRows("SELECT * FROM customers");
$template->vars['{saleCount}'] = $database->getNumberOfRows("SELECT * FROM sales");
// End Top Bar Values

$template->vars['{footerJS}'] .= '<script src="plugins/nazzer/chartgenerator.js"></script>'; // Import Charts Script


// Charts
lineChart_BooksByAuthor();
barChart_TotalsBooksSold();

$template->content();

function lineChart_BooksByAuthor() {
    global $functions, $database, $global;

    // Chart Variables
    $counter = 0;
    $authors = array();
    $books = array();
    $datasets = array();
    // End Chart Variables

    // Start Query
    $authorQuery = "SELECT authors.AuthorName, Count(*) FROM authors INNER JOIN books ON books.AuthorID = authors.AuthorID GROUP BY books.AuthorID";
    $authorQuery = $database->executeQuery($authorQuery);
    while($row = $authorQuery->fetch_array()) {
        $authors[$counter] = $row[0];
        $books[$counter] = $row[1];
        $counter++;
    }
    // End Query

    $datasets = array(
        $functions->generateChartDataSet(
            $books,
            "Number of Books"
        )
    );

    $functions->generateChart(
        $authors,
        $datasets,
        "Number of Books by Author",
        2
    );
}

function barChart_TotalsBooksSold() {
    global $functions, $database, $global;

    // Chart Variables
    $counter = 0;
    $bookNames = array();
    $bookValues = array();
    // End Chart Variables

    $bookQuery = "SELECT books.BookName, Count(*) FROM books INNER JOIN sales ON books.BookID=sales.BookID GROUP BY sales.BookID";
    $bookQuery = $database->executeQuery($bookQuery);
    while($book = $bookQuery->fetch_array()) {
        $bookNames[$counter] = $book[0];
        $bookValues[$counter] = $book[1];
        $counter++;
    }

    $dataSets = array(
        $functions->generateChartDataSet(
            $bookValues,
            "Sales per Book"
        )
    );

    $functions->generateChart(
        $bookNames,
        $dataSets,
        "Total Books Sold",
        0
    );
}
?>
