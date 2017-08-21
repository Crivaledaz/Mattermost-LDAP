<?php
$hostname = "ldap://ldap.company.com/";
$port = 389;

// variable use in connexion.php (rdn_suffix is often the same than base)
$rdn_suffix = 'ou=People,o=Company';

// variable use in resource.php (base is often the same than rdn_suffix)
$base = "o=Company";
$filter = "";

// ldap service user to allow search in ldap
$bind_dn = "";
$bind_pass = "";

//add virgule to concat in php script
if ($filter != "")
{
	$filter = "," . $filter;
}

if ($rdn_suffix != "")
{
	$rdn_suffix = "," . $rdn_suffix;
}