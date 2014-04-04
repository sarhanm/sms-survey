<?php
/**
 * Base file to import the vendor/autoload.php file
 */
# Autoloads all dependencies. Including our classes
date_default_timezone_set('America/Los_Angeles');
require_once __DIR__."/../vendor/autoload.php";
Logger::configure(__DIR__."/../config/log-config.xml");
define('SARHAN_SURVEY',true);
?>