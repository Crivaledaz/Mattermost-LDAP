<?php

$port  	  = intval(getenv('db_port')) ?: 5432;
$host  	  = getenv('db_host') ?: "127.0.0.1";
$name  	  = getenv('db_name') ?: "oauth_db";
$type	  = getenv('db_type') ?: "pgsql";
$username = getenv('db_user') ?: "oauth";
$password = getenv('db_pass') ?: "oauth_secure-pass";
$dsn	  = $type . ":dbname=" . $name . ";host=" . $host . ";port=" . $port;

/* Uncomment the line below to set date.timezone to avoid E.Notice raise by strtotime() (in Pdo.php)
 * If date.timezone is not defined in php.ini or with this function, Mattermost could return a bad token request error
*/
date_default_timezone_set ('Europe/Paris');
