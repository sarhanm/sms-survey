<?php
/**
 * Base file to import the vendor/autoload.php file
 */
// Autoloads all dependencies. Including our classes
// Required for the logj4 configuration.
// TODO: Need to change this so its not hard coded
date_default_timezone_set('America/Los_Angeles');

$autoload = __DIR__."/../vendor/autoload.php";

if(!file_exists($autoload))
{
    //Must be installed via composer. Need to go out a few more dirs
    $autoload = __DIR__.'/../../../autoload.php';
}

require_once $autoload;

Logger::configure(__DIR__."/../config/log-config.xml");

define('SARHAN_SURVEY',true);

unset($autoload);
?>