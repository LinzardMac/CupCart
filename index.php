<?php
/****************************************************
 * WhateverCart v0.1
 ****************************************************/

//  define dirs and other stuff
define("CART_VERSION", "0.1");
define("ROOT_DIR", dirname(__FILE__).DIRECTORY_SEPARATOR);
define("INCLUDES_DIR", ROOT_DIR.'cart-includes'.DIRECTORY_SEPARATOR);
define("PLUGINS_DIR", ROOT_DIR.'cart-contents'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR);
define("THEMES_DIR", ROOT_DIR.'cart-contents'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR);

//  include config
include(ROOT_DIR.'config.php');

//  define URL specific constants
define("THEMES_URI", ROOT_URI."cart-contents/themes/");

//  bootstrap environment
spl_autoload_register('cart_autoload', true, true);

function cart_autoload($className)
{
    $path = implode(DIRECTORY_SEPARATOR, explode("_", strtolower($className)));
    $src = INCLUDES_DIR.$path.'.php';
    if (file_exists($src) && is_file($src))
        include($src);
}

Core::run();