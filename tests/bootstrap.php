<?php
ini_set('display_errors', 1);
error_reporting(-1);

define('BASE_PATH', dirname(__DIR__));

set_include_path(implode(PATH_SEPARATOR, array(
    BASE_PATH . '/library',
    get_include_path()
)));

spl_autoload_register(function($className) {
    $path = str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $className) . '.php';
    include $path;
});