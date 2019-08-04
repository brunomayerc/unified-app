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
     * @param int $location_id Id of the location
     * @return mixed Listings information or Error if listing was not found
     */
    public function getAllListingsByLocation( $location_id = 0 ){

        // Check if id is valid and location exists
        $response = [];
        if( \key_exists( $location_id, $this->locations ) ){

            try {

                $response = Listing::getAll( $this->locations[ $location_id ]["url"] );

            } catch (\Exception $e) {
                
                // Something went wrong
                $response = [
                    "error"     => true,
                    "message"   => "Something went wrong when retrieving the listings: " . $e->getMessage()
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

    /**
     * Undocumented function
     *
     * @param string $listing_endpoint
     * @return void
     */
    public function getListingInfo( $listing_endpoint ){

        $response = [];
        if( filter_var( urldecode( $listing_endpoint ), FILTER_VALIDATE_URL ) ){

            try {

                $response = Listing::getListingInfo( urldecode( $listing_endpoint ) );

            } catch (\Exception $e) {
                
                // Something went wrong
                $response = [
                    "error"     => true,
                    "message"   => "Something went wrong when retrieving the listing info: " . $e->getMessage()
                ];

            }

        }else{
            
            // Location not found
            $response = [
                "error"     => true,
                "message"   => "Listing info endpoint is invalid: " . urldecode( $listing_endpoint )
            ];

        }

        return response()->json( $response );

    }
}
