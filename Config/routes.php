<?php

// Set default route controller
$router->add('/', 'App\Controllers\Home@index');
//$router->add('/home/(:value)/(:num)', 'App\Controllers\Home@index');

// Add custom routes
$router->add('/home', 'App\Controllers\Home@index');
$router->add('/form', 'App\Controllers\Home@index');
$router->add('/form/process', 'App\Controllers\Home@processForm', ['POST']);
$router->add('/report', 'App\Controllers\Report@index');
