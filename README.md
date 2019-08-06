# Dockerized PHP+ Lumen + Nginx app for Unified Compliance

## Overview

PHP application that scrapes the apartment listings for different locations (Portland, Miami & Las Vegas) from Craigslist and displays the listing results in a simple table that can be sorted.

## Setup

I chose to use [Docker](https://www.docker.com/) for running this application because I do not have any public server that I could use to deliver this app. 

In order to run this app all you need is to install [Docker Desktop](https://www.docker.com/products/docker-desktop).

### Build & Run

Download this repo or clone it into your computer
```bash
git clone https://github.com/brunomayerc/unified-app.git
```

After you have installed [Docker Desktop*](https://www.docker.com/products/docker-desktop)
```bash
cd unified-app
docker-compose up --build -d
```

*This may take a little bit, since docker needs to download the images needed for running this app (I talk more about this in the next section). 

****Once docker is done building, the application can be accessed on any browser by going to [http://localhost:30/**](http://localhost:30/)***

***Please make sure than any and all proxies are turned off*
***The aplication is being served from port :30 to avoid any conflicts with any other server that may be running on port :80(Apache) or :3000(Node).*

## How it works

### Docker

The docker setup for this app is pretty simple. All you need to run this is a HTTP Server (I chose [nginx](https://www.nginx.com/) over Apache for its superior performance) as well as the PHP runtime library (I went with the standard [php-fpm](https://php-fpm.org/)).
The docker config with the settings for each of the images can be found found in the `docker-compose.yaml`

### Lummen

Although the instructions did mention that no PHP Framework was needed for this task, I chose to use [Lumen](https://lumen.laravel.com/) (a super performant and barebones php framework) because I wanted to implement this app using a RESTFul API

### The Application

Here I'll attempt to explain how the application works as well as the code a little bit. I have also tried to include as much code docs as I could throughout the app. I'll talk about all the relevant files as well

#### Back-end & APIs

I chose to implement this aplication using a RESTful API, that contains 3 simple endpoints:

The endpoints configs can be found in this file:
[web.php#L16](https://github.com/brunomayerc/unified-app/blob/master/images/php/app/routes/web.php#L16)

All endpoints will execute their respective functions when accessed. All the functions can be found in this file:
[CraigsListController.php](https://github.com/brunomayerc/unified-app/blob/master/images/php/app/app/Http/Controllers/CraigsListController.php)

*API Endpoints*
* `api/locations`
Instead of hardcoding Portland, OR as the one location where the listings would be retrieved from, I abstracted this into a simple array with location configuration found in this file [CraigsListController.php#L20](https://github.com/brunomayerc/unified-app/blob/master/images/php/app/app/Http/Controllers/CraigsListController.php#L20)

Example call: [http://localhost:30/api/locations](http://localhost:30/api/locations)

* `api/listings[location_id]`
This is the endpoint that giving a location ID, it retrieves the Craigslist's endpoint URL and actually scrapes the site, parsing all the information needed into a simple array that is then served by the API in a JSON Format. The logic for this can be found in this file [CraigsListController.php#L44](https://github.com/brunomayerc/unified-app/blob/master/images/php/app/app/Http/Controllers/CraigsListController.php#L44)

Example call: [http://localhost:30/api/listings/1](http://localhost:30/api/listings/1)

* `api/info[location_endpoint]`
This is the endpoint that giving a Craigslist detail endpoint actually reaches out to a **listing's detail page** and scrapes it, retrieving all the metadata necessary into a simple array that is then served by the API in a JSON Format. The logic for this can be found in this file [CraigsListController.php#L84](https://github.com/brunomayerc/unified-app/blob/master/images/php/app/app/Http/Controllers/CraigsListController.php#L84)

Example call: [http://localhost:30/api/info/https%3A%2F%2Flasvegas.craigslist.org%2Fapa%2Fd%2Flas-vegas-10-days-free-water-trash%2F6949567476.html](http://localhost:30/api/info/https%3A%2F%2Flasvegas.craigslist.org%2Fapa%2Fd%2Flas-vegas-10-days-free-water-trash%2F6949567476.html)

#### Front-end

The front-end is very simple and it is only one file: [index.blade.php](https://github.com/brunomayerc/unified-app/blob/master/images/php/app/resources/views/index.blade.php)

It has a `locations dropdown` and a `listings table`.

* The `locations dropdown` is initially empty, and once the home page is loaded, an async ajax request is made to [http://localhost:30/api/locations](http://localhost:30/api/locations) retrieving the available locations and adding them to the `locations dropdown`

* The `locations dropdown` also has a `change` event bound to it, that when the users selects a locations, an async ajax request is made to [http://localhost:30/api/listings](http://localhost:30/api/listings) retrieving all the listings available in the locations home page on CraigsList and adds them to the `listings table`. **Initially, the listings only contain the listing's title and placeholders for all the other information**

* For every listing that is now in the `listings table`, an additional async ajax request is made to [http://localhost:30/api/info](http://localhost:30/api/info) with the listing's endpoint, finally retrieving the metadata for each listing. You will notice a loading animation as each listing has its information retrieved.
**I decided to separate the listings call from the details call because of the amount of listings on the home page (120) could cause the server to timeout or crash, and even if it didn't I would've made the same choice**

* The `listings table` can then be sorted (done using javascript) by clicking in the table headers that have the color blue.


