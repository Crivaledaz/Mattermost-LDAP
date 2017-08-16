<?php
$hostname = "ldap://company.com/";
$port = 389;

// variable use in connexion.php 
$rdn_suffix = 'ou=People,o=Company';

// variable use in resource.php 
$base = "o=Company";
$filter = "";

//add coma to concat in php script
if ($filter != "")
{
	$filter = "," . $filter;
}

if ($rdn_suffix != "")
{
	$rdn_suffix = "," . $rdn_suffix;
}