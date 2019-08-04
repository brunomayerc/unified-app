<?php

namespace App;

/**
 * Library used for parsing html dom nodes
 * 
 * @see https://github.com/paquettg/php-html-parser
 */
use PHPHtmlParser\Dom;

class Listing 
{      

    /**
     * Contains the nodes to find the listings in the order they appear on CL
     */
    const CL_LISTING_TREE = [ "#sortable-results", ".rows", "li" ];
    
    /**
     * URL Pattern for CL thumbnail images
     */
    const CL_THUMBNAIL_ULR = "https://images.craigslist.org/%s_300x300.jpg";
    
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

        // Loop trough all the listings found
        $listings = [];
        foreach ( $this->findListings() as $listing ) {

            $listings[] = [
                "title"         => $this->getNodeInfo( $listing, ".result-title", "text" ),
                "url"           => $this->getNodeInfo( $listing, ".result-title", "href" ),
                "bedrooms"      => $this->getNodeInfo( $listing, ".housing", "text" ),
                "cost"          => $this->getNodeInfo( $listing, ".result-price", "text" ),
                "location"      => $this->getNodeInfo( $listing, ".result-hood", "text" ),
                "thumbnails"    => $this->formatThumbnails( $this->getNodeInfo( $listing, ".result-image", "outerHtml" ) )
            ];

        }

        return $listings;

    }
    

    /**
     * Giving a node, finds the images information based on CL pattern
     *
     * @param mixed $node Node containing the images info
     * @return array Thumbnails urls
     */
    private function formatThumbnails( $node ) {

        $startDelimiter = "1:";
        $endDelimiter = ",";
        $contents = array();
        $startDelimiterLength = strlen( $startDelimiter );
        $endDelimiterLength = strlen( $endDelimiter );
        $startFrom = $contentStart = $contentEnd = 0;
        while ( false !== ( $contentStart = strpos( $node, $startDelimiter, $startFrom ) ) ) {
            $contentStart += $startDelimiterLength;
            $contentEnd = strpos( $node, $endDelimiter, $contentStart );
            if ( false === $contentEnd ) break;
            $contents[] = sprintf( self::CL_THUMBNAIL_ULR, substr($node, $contentStart, $contentEnd - $contentStart) );
            $startFrom = $contentEnd + $endDelimiterLength;
        }

        return $contents;
    }

    /**
     * Giving a dom node, retrieves information from it
     *
     * @param mixed $node The DOM node
     * @param string $selector The selector within that node where the info is located
     * @param string $attr What attr from that node is being retrieved
     * @return string|bool Node info if found or false if not found
     */
    private function getNodeInfo( $node, $selector, $attr ){

        $nodeInfo = false;
        $info = $node->find( $selector );
        if ( count( $info ) > 0 ) {

            $nodeInfo = trim( $info[0]->{$attr} );

        }

        return $nodeInfo;

    }
    
    /**
     * Find and retrieves all the listing nodes within CL
     *
     * @return array listing nodes found on CL
     */
    private function findListings(){

        $listing_nodes = $this->location_rawHTMLDom;
        foreach ( self::CL_LISTING_TREE as $selector ) {
            
            // Check if the node exists
            if ( empty( $listing_nodes ) ) {

                throw new \Exception("Craigslist listing node tree is invalid: " . $selector);

            }

            $listing_nodes = $listing_nodes->find( $selector );

        }

        return $listing_nodes;

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
            
            throw new \Exception("Error retrieving data from Craigslist. " . $e->getMessage() );
            
        }

    }
    
}