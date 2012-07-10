<?php
/****************************************************
 * WhateverCart v0.1
 ****************************************************/

//  define dirs and other stuff
define("CC_VERSION", "0.1");
define("CC_ROOT_DIR", dirname(__FILE__).DIRECTORY_SEPARATOR);
define("CC_INCLUDES_DIR", CC_ROOT_DIR.'cart-includes'.DIRECTORY_SEPARATOR);
define("CC_PLUGINS_DIR", CC_ROOT_DIR.'cart-contents'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR);
define("CC_MUPLUGINS_DIR", CC_ROOT_DIR.'cart-contents'.DIRECTORY_SEPARATOR.'mu-plugins'.DIRECTORY_SEPARATOR);
define("CC_THEMES_DIR", CC_ROOT_DIR.'cart-contents'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR);

//  include config
include(CC_ROOT_DIR.'config.php');

//  define URL specific constants
define("CC_THEMES_URI", CC_ROOT_URI."cart-contents/themes/");

//  bootstrap environment
spl_autoload_register('cart_autoload', true, true);

function cart_autoload($className)
{
    $path = implode(DIRECTORY_SEPARATOR, explode("_", strtolower($className)));
    $src = CC_INCLUDES_DIR.$path.'.php';
    if (file_exists($src) && is_file($src))
        include($src);
}

Core::run();