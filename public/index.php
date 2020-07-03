<?php

/*
 * ---------------------------------------------------------------
 * INCLUDE COMPOSER AUTOLOAD
 * ---------------------------------------------------------------
 *
 * Autoload core classes with composer
 *
 */

define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';

defined('APP_PATH') || define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'App');
defined('VIEW_PATH') || define('VIEW_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'Views');


/*
 * ---------------------------------------------------------------
 * ERROR & EXCEPTION HANDLE
 * ---------------------------------------------------------------
 *
 *  Set custom error & exception handler functions
 *
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/*
 * ---------------------------------------------------------------
 * INCLUDE APPLICATION HELPERS
 * ---------------------------------------------------------------
 *
 * Include all helper files as required file
 *
 */
$helperFiles = glob(ROOT_PATH . '/Helper/*.php');
if (!empty($helperFiles)) {
    foreach ($helperFiles as $helper) {
        require_once($helper);
    }
}

if (!file_exists(ROOT_PATH . '/Helper/system_helper.php')) {
    // Throw error if main system helper file not found
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    die('system_helper.php file not found');
}


/*
 * ---------------------------------------------------------------
 * INITIALIZE APPLICATION ENVIRONMENT
 * ---------------------------------------------------------------
 *
 * Parse all environment parameters from .env file
 * Convert them into application constants
 *
 */
$envFilePath = ROOT_PATH . '/.env';
if (file_exists($envFilePath) && is_readable($envFilePath)) {
    parseEnv($envFilePath);
} else {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    die('.env file not found');
}

/*
 * ---------------------------------------------------------------
 * ERROR REPORTING
 * ---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting
 * By default development will show errors but testing and live will hide them
 */
switch (ENV) {
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', '1');
        break;

    case 'testing':
    case 'production':
        ini_set('display_errors', '0');
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', true, 503);
        die('Environment is not set correctly in .env file');
}


/*
 * ---------------------------------------------------------------
 * INITIALIZE APPLICATION ROUTER
 * ---------------------------------------------------------------
 */
$router = new Core\Router();


/*
 * ---------------------------------------------------------------
 * INCLUDE ROUTING CONFIGURATION FILE
 * ---------------------------------------------------------------
 */
$routeFile = ROOT_PATH . '/Config/routes.php';
if (file_exists($routeFile) && is_readable($routeFile)) {
    require_once($routeFile);
} else {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    die('Config/routes.php file not found');
}

// Start Application Session
session_start();

// Set server timezone if configured
if (defined('TIMEZONE')) {
    date_default_timezone_set(TIMEZONE);
}

/*
 * ---------------------------------------------------------------
 * RUN MVC APPLICATION
 * ---------------------------------------------------------------
 *
 * Execute action controller
 *
 */
$router->dispatch();
