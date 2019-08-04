<?php

namespace App\Http\Controllers;

use App\Listing;

class CraigsListController extends Controller
{

    /**
     * Stores all existing locations
     *
     * @var array of Location objects
     */
    private $locations = array();

    public function __construct()
    {
        // Available locations
        $this->locations = [
            [ "id" => "0", "name" => "Portland, OR", "url" => "https://portland.craigslist.org/d/apts-housing-for-rent/search/apa" ],
            [ "id" => "1", "name" => "Miami, FL", "url" => "https://miami.craigslist.org/d/apts-housing-for-rent/search/apa" ],
            [ "id" => "2", "name" => "Las Vegas, NV", "url" => "https://lasvegas.craigslist.org/d/apts-housing-for-rent/search/apa" ],
        ];
    }

    /**
     * Retrieves all the available locations
     *
     * @return array Locations information
     */
    public function getLocations(){

        return response()->json( $this->locations );

    }

    /**
     * Retrieves 
     *
     * @param [type] $id Id of the location
     * @return mixed Listings information or Error if listing was not found
     */
    public function getAllListingsByLocation( $id = 0 ){

        // Check if id is valid and location exists
        $response = [];
        if( \key_exists( $id, $this->locations ) ){

            try {
                
                // Creates the listing based on the location info
                $listing = new Listing( $id, $this->locations[ $id ] );

                $response = $listing->getAll();

            } catch (\Exception $e) {
                
                // Something went wrong
                $response = [
                    "error"     => true,
                    "message"   => "Something went wrong when retrieving the listing: " . $e->getMessage()
                ];

            }

        }else{
            
            // Location not found
            $response = [
                "error"     => true,
                "message"   => "Location not found. Please try again."
            ];

        }

        return response()->json( $response );

    }
}
