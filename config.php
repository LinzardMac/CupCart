<?php
/*
    CupCart configuration file. Only edit if you know what you're doing.
*/
//  root URI, must end with a slash
define("CC_ROOT_URI", "/whatevercart/");
//  set this to empty if using mod_rewrite or similar
define("CC_INDEX_FILE", "index.php");

//  database credentials
define("CC_DB_USER", "cupcart");
define("CC_DB_PASS", "cupcart");
define("CC_DB_NAME", "cupcart");
define("CC_DB_DSN", "mysql:dbname=".CC_DB_NAME.";host=localhost");
define("CC_DB_PREFIX", "cup_");
define("CC_DB_PERSISTENT", false);