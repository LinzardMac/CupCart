<?php
/*
    CupCart configuration file. Only edit if you know what you're doing.
*/
//  root URI, must end with a slash
define("ROOT_URI", "/whatevercart/");
//  set this to empty if using mod_rewrite or similar
define("INDEX_FILE", "index.php");

//  database credentials
define("DB_USER", "cupcart");
define("DB_PASS", "cupcart");
define("DB_DATABASE", "cupcart");
define("DB_DSN", "mysql:dbname=".DB_DATABASE.";host=localhost");
define("DB_PREFIX", "cup_");
define("DB_PERSISTENT", false);