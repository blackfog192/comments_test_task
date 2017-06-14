<?php

// General settings
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connect system files
define('ROOT', dirname(__FILE__));
require_once(ROOT .'/core/router.php');
require_once(ROOT .'/core/db_connect.php');

// Calling router
$router = new Router();
$router->run();

?>