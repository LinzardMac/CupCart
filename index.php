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
define("CC_ADMIN_THEMES_DIR", CC_ROOT_DIR.'cart-contents'.DIRECTORY_SEPARATOR.'admin-themes'.DIRECTORY_SEPARATOR);

//  include config
include(CC_ROOT_DIR.'config.php');

//  quickly check if we need to forward to /index.php without running the core
$checkStr = CC_ROOT_URI.CC_INDEX_FILE;
if (substr($_SERVER['REQUEST_URI'], 0, strlen($checkStr)) != $checkStr)
{
    header("Location: ".CC_ROOT_URI.CC_INDEX_FILE);
    exit;
}

//  define URL specific constants
define("CC_THEMES_URI", CC_ROOT_URI."cart-contents/themes/");
define("CC_ADMIN_THEMES_URI", CC_ROOT_URI."cart-contents/admin-themes/");

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