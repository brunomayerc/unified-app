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
     * Retrieves all listings 
     *
     * @param string $location_endpoint Location's listings url
     * @return array All listings
     */
    public static function getAll( $location_endpoint ){

        // Retrieves data from CL
        $location_rawHTMLDom = self::getRawHTMLFromEndpoint( $location_endpoint );

        // Loop trough all the listings found
        $listings = [];
        foreach ( self::findListings( $location_rawHTMLDom ) as $listing ) {

            $listings[] = [
                "title"         => self::getNodeInfo( $listing, ".result-title", "text" ),
                "url"           => self::getNodeInfo( $listing, ".result-title", "href" ),
                "bedrooms"      => self::getNodeInfo( $listing, ".housing", "text" ),
                "cost"          => self::getNodeInfo( $listing, ".result-price", "text" ),
                "location"      => self::getNodeInfo( $listing, ".result-hood", "text" ),
                "thumbnails"    => self::formatThumbnails( self::getNodeInfo( $listing, ".result-image", "outerHtml" ) )
            ];

        }

        return $listings;

    }

    /**
     * Retrieves a listing information
     *
     * @param string $listing_endpoint Listing's info url
     * @return array Listing's information
     */
    public static function getListingInfo( $listing_endpoint ) {

        // Retrieves data from CL
        $listing_rawHTMLDom = self::getRawHTMLFromEndpoint( $listing_endpoint );

        return [
            "bedrooms"      => self::getNodeInfo( $listing_rawHTMLDom, ".housing", "text" ),
            "cost"          => self::getNodeInfo( $listing_rawHTMLDom, ".price", "text" ),
            "location"      => self::getNodeInfo( $listing_rawHTMLDom, ".mapaddress", "text" ),
            "thumbnail"     => self::getNodeInfo( $listing_rawHTMLDom, "img", "src" )
        ];

    }

    /**
     * Giving a node, finds the images information based on CL pattern
     *
     * @param mixed $node Node containing the images info
     * @return array Thumbnails urls
     */
    private static function formatThumbnails( $node ) {

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
    private static function getNodeInfo( $node, $selector, $attr ){

        $nodeInfo = false;
        $info = $node->find( $selector );
        if ( count( $info ) > 0 ) {

            $nodeInfo = ltrim ( rtrim( trim ( $info[0]->{$attr} ) , "-" ) , "/" );

        }

        return trim ( $nodeInfo );

    }
    
    /**
     * Find and retrieves all the listing nodes within CL
     *
     * @return array listing nodes found on CL
     */
    private static function findListings( $listing_dom ){

        foreach ( self::CL_LISTING_TREE as $selector ) {
            
            // Check if the node exists
            if ( empty( $listing_dom ) ) {

                throw new \Exception("Craigslist listing node tree is invalid: " . $selector);

            }

            $listing_dom = $listing_dom->find( $selector );

        }

        return $listing_dom;

    }


    /**
     * Retrieves the raw HTML from an endpoint and transforms it into a DOM Object
     *
     * @return void
     */
    private static function getRawHTMLFromEndpoint( $endpoint ){

        try {
            
            $dom = new Dom();
            return $dom->loadFromUrl( $endpoint );    
            
        } catch ( \Exception $e ) {
            
            throw new \Exception("Error retrieving data from Craigslist. " . $e->getMessage() );
            
        }

    }
    
}