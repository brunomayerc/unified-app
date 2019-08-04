<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

$router->get( '/' , function () use ($router) {
    return view('index', ['name' => 'James']);
});

/**
 * RESTful API Endpoints
 */
$router->group([ 'prefix' => 'api' ], function () use ( $router ) {
    
    // Retrieve all available locations
    $router->get( 'locations',  [ 'uses' => 'CraigsListController@getLocations' ] );

    // Retrieve all listings for a location
    $router->get( 'listings[/{location_id}]',  [ 'uses' => 'CraigsListController@getAllListingsByLocation' ] );

    // Retrieves a listing's info
    $router->get( 'info[/{listing_endpoint}]',  [ 'uses' => 'CraigsListController@getListingInfo' ] );

});