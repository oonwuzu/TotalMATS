<?php
error_log('REQUEST ' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
require_once 'conf/conf.php';

define('ROOT_PATH', dirname(__FILE__) . '/');

$controller = isset($_GET['c']) ? $_GET['c'] : DEFAULT_CONTROLLER;
$method = isset($_GET['m']) ? $_GET['m'] : DEFAULT_METHOD;

require_once 'src/controllers/' . $controller . '.php';

$controller = new $controller();

if (isset($_GET['args'])) {
    $args = explode('|', $_GET['args']);
    // objects / arrays need to be unserialized
    foreach ($args as &$arg) {
        $unserialized = @unserialize($arg);
        if ($unserialized !== false) {
            $arg = $unserialized;
        }
    }
} else {
    $args = array();
}

call_user_func_array(array($controller, $method), $args);
?>
