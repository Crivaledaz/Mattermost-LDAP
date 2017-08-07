<?php
/**
 * @author Denis CLAVIER <clavierd at gmail dot com>
 * Adapted from Oauth2-server-php cookbook
 * @see http://bshaffer.github.io/oauth2-server-php-docs/cookbook/
 */

// include our OAuth2 Server object
require_once __DIR__.'/server.php';

// include our LDAP object
require_once __DIR__.'/LDAP/LDAP.php';
require_once __DIR__.'/LDAP/config_ldap.php';

// Handle a request to a resource and authenticate the access token
if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    die;
}

// set default error message
$resp = array("error" => "Unknow error", "message" => "An unknown error has occured, please report this bug");

// get information on user associated to the token
$info_oauth = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());
$uid = $info_oauth["user_id"];
$assoc_id = $info_oauth["assoc_id"];

//##################################################\\
//    /!\   Adapt here with your LDAP config   /!\  \\
//##################################################\\

$base = "o=Company";
$filter = "uid=" . $uid;

/****************************************************/

// Open a LDAP connection
$ldap = new LDAP($hostname,$port);

// Try to get user data on the LDAP
try
{
	$data = $ldap->getDataForMattermost($base,$filter);
	$resp = array("name" => $data['cn'],"username" => $uid,"id" => $assoc_id,"state" => "active","email" => $data['mail']);
}
catch (Exception $e)
{
	$resp = array("error" => "Impossible to get data", "message" => $e->getMessage());
}

// send data or error message in JSON format
echo json_encode($resp);