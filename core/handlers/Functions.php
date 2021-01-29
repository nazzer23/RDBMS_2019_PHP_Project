<?php

class Functions
{
    private $global;

    public function __construct($global)
    {
        $this->global = $global;
    }

    // Homepage functions
    public function generateBookCard($bookObject)
    {
        $linkData[0]["url"] = "/?book&id=" . $bookObject->BookID;
        $linkData[0]["text"] = "More Info";

        $data = '';
        $data .= '<small>By ' . $this->getAuthorByID($bookObject->AuthorID) . '</small>';
        $data .= '<p>Price: £' . $bookObject->BookPrice . '</p>';

        $body = $this->generateCardBody($bookObject->BookName, $data, $linkData);

        $imgData["img"] = "/images/books/".$bookObject->BookISBN.".jpg";
        $imgData["side"] = "top";

        $builder = $this->generateCard($body, "18em", true, $imgData);

        return $builder;
    }

    // Search Functions

    public function getAuthorByID($authorID)
    {
        global $main;
        $query = "SELECT AuthorName FROM authors WHERE AuthorID={$authorID}";
        $query = $main->db->fetchObject($query);
        return $query->AuthorName;
    }

    // Private Functions

    public function generateCardBody($title = null, $text = null, $link = null)
    {
        $body = "";
        if ($title != null) {
            $body .= '<h5 class="card-title">' . $title . '</h5>';
        }
        if ($text != null) {
            $body .= '<p class="card-text">' . $text . '</p>';
        }
        if ($link != null) {
            foreach ($link as $values) {
                $body .= '<div class="text-right"><a href="' . $values["url"] . '" class="btn btn-primary">' . $values["text"] . '</a></div>';
            }
        }
        return $body;
    }

    // Global Functions
    public function generateCard($cardBody, $width = null, $center = false, $image = null)
    {
        $card = "";
        if ($center) {
            $card .= '<div class="col d-flex justify-content-center">';
        }
        if ($width == null) {
            if ($image["side"] == "left") {
                //flex-md-row
                $card .= '<div class="card flex-md-row mb-4 box-shadow h-md-250">';
            } else {
                $card .= '<div class="card mb-4 box-shadow h-md-250">';
            }
        } else {
            if ($image["side"] == "left") {
                $card .= '<div class="card flex-md-row mb-4 box-shadow h-md-250" style="width: ' . $width . '">';
            } else {
                $card .= '<div class="card mb-4 box-shadow h-md-250" style="width: ' . $width . '">';
            }
        }
        if ($image != null) {
            $card .= '<img class="card-img-' . $image['side'] . ' flex-auto d-none d-md-block img-responsive img-thumbnail rounded mx-auto d-block" style="width:200px;" src="' . $image['img'] . '" alt="Book Cover">';
        }
        $card .= '<div class="card-body">';
        $card .= $cardBody;
        $card .= '</div>';
        $card .= '</div>';
        if ($center) {
            $card .= '</div>';
        }
        return $card;
    }

    public function generateSearchCard($bookObject)
    {
        // Links
        // More Info
        $linkData[0]["url"] = "/?book&id=" . $bookObject->BookID;
        $linkData[0]["text"] = "More Info";

        // Add to Cart
        if($bookObject->QuantityRemain > 0) {
            $linkData[1]["url"] = "/?book&id=" . $bookObject->BookID . "&addToCart=true";
            $linkData[1]["text"] = "Add to Cart";
        }

        $data = "<small>By " . $this->getAuthorByID($bookObject->AuthorID) . "</small>";
        $data .= '<p>Price: £' . $bookObject->BookPrice . '</p>';
        $data .= '<p>ISBN: ' . $bookObject->BookISBN;

        $body = $this->generateCardBody($bookObject->BookName, $data, $linkData);

        $imgData["img"] = "/images/books/".$bookObject->BookISBN.".jpg";
        $imgData["side"] = "left";

        $builder = $this->generateCard($body, "32em", true, $imgData);

        return $builder;
    }
}

?>