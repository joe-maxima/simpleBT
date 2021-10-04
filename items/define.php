<?php
// Available for PHP 7.4.3

// DB connection strings
define("HOST","mysql:host=localhost;port=3306;dbname=");
define("DBNAME","YourDBname");
define("USER","DBuser");
define("PASS","DBpassword");

// server config
if(!defined("ROOT_PATH")){
	define("ROOT_PATH","/var/www/html/lsbt/");
}
if(!defined("SERVICE_ROOT")){
	define("SERVICE_ROOT","http://xxx.xxx.xxx.xxx/simpleBT/");
}

// smarty file path
define("SMARTY_DIR","/var/simpleBT/libs/");
define("VIEW_ROOT","/var/simpleBT/");
define("LOG_PATH","/var/log/simpleBT/");

// debug settings
define("DEBUG_MODE",False);

?>
