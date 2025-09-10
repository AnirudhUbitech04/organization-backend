<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// API routes
$routes->get('api/modules', 'ModuleController::index');
$routes->get('api/auth', 'Auth::index');       // GET all organizations
$routes->post('api/login', 'Auth::login');    // POST login
$routes->options('(:any)', 'Auth::options');
$routes->post('api/logout/(:num)', 'Auth::logout/$1');
$routes->options('api/logout/(:num)', 'Auth::options/$1'); // For CORS preflight

$routes->get('api/countries', 'Location::countries');
$routes->get('api/states/(:num)', 'Location::states/$1');
$routes->get('api/cities/(:num)', 'Location::cities/$1'); // catch-all for OPTIONS
   

// $routes->options('(:any)', 'Employee::options');
$routes->group('employee', function($routes) {
    // Step 1: Insert Personal Info
    $routes->post('insert', 'Employee::insert');

    // Step 2: Update Additional Info
    $routes->post('update', 'Employee::update');

    // Step 3: Preview a record (optional ID)
    $routes->get('preview/(:num)', 'Employee::preview/$1'); // preview by ID
    $routes->get('preview', 'Employee::preview');  

   $routes->get('getImage/(:any)', 'Employee::getImage/$1');

        // last inserted record if no ID

    // Optional test route
    $routes->get('test', 'Employee::test');
});
$routes->group('todo', function($routes) {
    $routes->get('', 'Todo::index');
    $routes->post('create', 'Todo::create');
    $routes->post('update/(:num)', 'Todo::update/$1');
    $routes->get('status-summary', 'Todo::statusSummary');
});






