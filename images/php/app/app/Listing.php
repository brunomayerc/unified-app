<?php

namespace App;

use PHPHtmlParser\Dom;

class Listing 
{      
    /**
     * Location identifier
     *
     * @var int
     */
    private $location_id;

    /**
     * Location name
     *
     * @var int
     */
    private $location_name;

    /**
     * Location url endpoint where listings are retrieved from
     *
     * @var int
     */
    private $location_endpoint;

    /**
     * Location raw html retrieved from the CL endpoint into a DOM Tree
     *
     * @var PHPHtmlParser\Dom
     */
    private $location_rawHTMLDom;

    /**
     * Constructor
     *
     * @param int $id Location id
     * @param array $location Array containing the location information
     */
    public function __construct( $id, $location )
    {
        try {

            $this->location_id = $id;
            $this->location_name = $location[ "name" ];
            $this->location_endpoint = $location[ "url" ];
    
        } catch (\Exception $e) {

            throw new \Exception("Listing location is invalid. Please try again.");

        }

    }

    /**
     * Retrieves all listings 
     *
     * @return array Listings information
     */
    public function getAll(){

        // Retrieves data from CL
        $this->setLocation_rawHTMLDom();
        
        // Loop trough the CL, formatting them
        die(count($this->location_rawHTMLDom->find('#sortable-results')->find(".rows")->find("li")) . "-");

    }


    /**
     * Retrieves the raw HTML from the location endpoint and transforms it into a DOM Object
     *
     * @return void
     */
    private function setLocation_rawHTMLDom(){

        try {
            
            $dom = new Dom();
            $this->location_rawHTMLDom = $dom->loadFromUrl( $this->location_endpoint );    
            
        } catch ( \Exception $e ) {
            
            throw new \Exception("Error retrieving data from Craigslist. Please try again.");
            
        }

    }
    
}