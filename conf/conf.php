<?php

require_once 'conf_dev.php';
//require_once 'conf_prod.php';

define('APP_NAME', 'TotalMats');
define('APP_ID', '7045');
define('DEV_ID', '19647');

//if (empty($_COOKIE['binusys_home_app_url'])) {
//    define('HOME_URL', 'http://apps.binu.net/apps/mybinu/index.php');
//} else {
//    define('HOME_URL', $_COOKIE['binusys_home_app_url']);
//}
//define('HOME_URL', 'http://apps.binu.net/apps/mybinu/index.php?brand=gtbank');
define('HOME_URL', 'http://swifta.co/matsapp');

define('DEFAULT_CONTROLLER', 'Pages');
define('DEFAULT_METHOD', 'home');

if (empty($_COOKIE['binusys_size'])) {
    $width = 320;
    $height = 240;
} else {
    list($width, $height) = explode('x', $_COOKIE['binusys_size']);
}
define('SCREEN_WIDTH', $width);
define('SCREEN_HEIGHT', $height);

if (empty($_COOKIE['binusys_display'])) {
    define('SCBAR_W', 0);
} else {
    preg_match('/scbarW:(\d*)\|/', $_COOKIE['binusys_display'], $matches);
    define('SCBAR_W', $matches[1]);
}

define('BANNER_ASPECT_RATIO', 6.25);

define('BG_COLOR', '#ffffff');
//define('FOOTER_BG_COLOR', '#f46a19');
define('FOOTER_BG_COLOR', '#1562a5');
define('FOOTER_TEXT_COLOR', '#ffffff');
//define('BUTTON_COLOR', '#f46a19');
define('ALT_BRAND_COLOR','#C75D4D');
define('BUTTON_COLOR', '#1562a5');
define('BORDER_COLOR', '#dddddd');
