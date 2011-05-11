<?php

define("APPLICATION", __DIR__);
define("ROOT", __DIR__ . "/..");
define("WEBROOT", ROOT . "/public");
define("LIBRARY", ROOT . "/library");

ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL | E_STRICT);

session_start();
ob_start();

/**
 * Autoloading
 */
require_once LIBRARY . "/WebFW/Loader.php";
$loader = WebFW\Loader::getInstance();
$loader->addNamespaces(array(
    "SampleApp" => APPLICATION,
    "WebFW" => LIBRARY . "/WebFW"
));
$loader->register();

/**
 * Setup
 */
WebFW\View::$defaultPath = APPLICATION . "/views/";
WebFW\Registry::set("layout", new WebFW\View("layout.php"));