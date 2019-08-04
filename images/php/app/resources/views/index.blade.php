<!-- sourced from https://raw.githubusercontent.com/reactjs/reactjs.org/master/static/html/single-file-example.html -->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Craigslist APP | Unified Compliance </title>

        <!-- FAVICON -->
        <link rel="shortcut icon" type="image/png" href="https://www.unifiedcompliance.com/favicon.png" />
        <link rel="shortcut icon" href="https://www.unifiedcompliance.com/favicon.ico" type="image/x-icon" />
        <link rel="icon" href="https://www.unifiedcompliance.com/favicon.ico" type="image/x-icon"/>

        <!-- PURE.css -->
        <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.1/build/pure-min.css" integrity="sha384-oAOxQR6DkCoMliIh8yFnu25d7Eq/PHS21PClpwjOTeU2jRSq11vu66rf90/cZr47" crossorigin="anonymous">

        <!-- Small custom css -->
        <style>
            html, button, input, select, textarea, .pure-g {
                /* Set your content font stack here: */
                font-family: Georgia, Times, "Times New Roman", serif;
            }
            .pure-g {
                max-width: 1200px;
                margin: 20px auto;
            }
            .logo {
                max-height: 65px;
                margin: 20px 0;
            }
            .pure-table {
                width: 100%;
            }
            #listings img {
                max-width: 150px;
            }
        </style>

        <!-- Small custom js -->
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                
                const locations = document.getElementById('locations');
                const listings = document.getElementById('listings');

                /**
                 * Loads all locations
                 */
                const loadLocations = () => {

                    fetch(`/api/locations`)
                    .then(res => res.json())
                    .then(locationData => {

                        locationData.map(location => {
                            const opt = document.createElement('option');
                            opt.value = location.id;
                            opt.innerHTML = location.name;
                            locations.appendChild(opt);
                        });

                    });

                }

                /**
                 * Loads all the listings for a specific location
                 */
                const loadListings = location_id => {

                    listings.innerHTML = `<tr><td colspan="6" align="center"><h3>Retrieving listings...</h3></td></tr>`;

                    fetch(`/api/listings/${location_id}`)
                    .then(res => res.json())
                    .then(listingData => {

                        listings.innerHTML = '';

                        listingData.map(listing => {

                            const row = document.createElement('tr');

                            let column = document.createElement('td');
                            column.innerHTML =  ( listing.thumbnails.length ) ? `<img src="${listing.thumbnails[0]}" />` : `<b>No Image</b>`;
                            row.appendChild(column);

                            column = document.createElement('td');
                            column.innerHTML = listing.title;
                            row.appendChild(column);

                            column = document.createElement('td');
                            column.innerHTML =  ( listing.bedrooms ) ? listing.bedrooms : `<b>No Info</b>`;
                            row.appendChild(column);

                            column = document.createElement('td');
                            column.innerHTML =  ( listing.cost ) ? listing.cost : `<b>No Info</b>`;
                            row.appendChild(column);

                            column = document.createElement('td');
                            column.innerHTML =  ( listing.location ) ? listing.location : `<b>No Info</b>`;
                            row.appendChild(column);

                            column = document.createElement('td');
                            column.innerHTML = `<a class="pure-button pure-button-primary" target='_blank' href="${listing.url}">More</a>`;
                            row.appendChild(column);

                            listings.appendChild(row);
                        });

                    });

                }

                // Loads the locations into the select
                loadLocations();

                // Binds the loading of the listings to the select
                locations.addEventListener("change", event => {
                    if(event.target.value !== ""){
                        loadListings(event.target.value);
                    }
                });

            }, false);
        </script>
    </head>

    <body>
        <div class="pure-g">
            <div class="pure-u-1-2">
                <img class="logo" src="https://www.unifiedcompliance.com/wp-content/uploads/2017/10/mast-logo.png">
            </div>
        </div>
        <div class="pure-g">
            <div class="pure-u-1-1">
                <form class="pure-form">
                    <fieldset>
                        <label for="locations">Select the location: </label>
                        <select id="locations" class="pure-input-1-4">
                            <option value="">Select</option>
                        </select>
                    </fieldset>
                </form>
            </div>
        </div>
        <div class="pure-g">
            <div class="pure-u-1-1">
                <table  class="pure-table pure-table-horizontal">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Title</th>
                            <th>Bedrooms</th>
                            <th>Cost</th>
                            <th>Location</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody id="listings"></tbody>
                </table>
            </div>
        </div>
    </body>
</html>