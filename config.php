<?php
if (session_status() == PHP_SESSION_NONE) session_start();

if (!defined('DB_TYPE'))  define('DB_TYPE', 'mysql');
if (!defined('DB_HOST'))  define('DB_HOST', 'localhost');
if (!defined('DB_PORT'))  define('DB_PORT', '3306');
if (!defined('DB_NAME'))  define('DB_NAME', 'weblog');
if (!defined('DB_USER'))  define('DB_USER', 'root');
if (!defined('DB_PASS'))  define('DB_PASS', 'aaaaaaaa');
if (!defined('ROOT_PATH')) define('ROOT_PATH', realpath(dirname(__FILE__)));
if (!defined('BASE_URL'))  define('BASE_URL', 'http://localhost/');

if (!isset($conn)) {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
}


//define some constants:
if (!defined('ROOT_PATH'))  define('ROOT_PATH', realpath(dirname(__FILE__)));
if (!defined('BASE_URL'))  define('BASE_URL', 'http://localhost/');

?>
