<?php

$port  	  = 5432;
$host  	  = "localhost";
$name  	  = "oauth_db";
$type	  = "pgsql";
$username = "oauth";
$password = "oauth_secure-pass";
$dsn	  = $type . ":dbname=" . $name . ";host=" . $host . ";port=" . $port; 

/* Uncomment the line below to set date.timezone to avoid E.Notice raise by strtotime() (in Pdo.php)
 * If date.timezone is not defined in php.ini or with this function, Mattermost could return a bad token request error
*/
//date_default_timezone_set ('Europe/Paris');