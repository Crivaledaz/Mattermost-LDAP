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
$resp = array("error" => "Unknown error", "message" => "An unknown error has occured, please report this bug");

// get information on user associated to the token
$info_oauth = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());
$user = $info_oauth["user_id"];
$assoc_id = intval($info_oauth["assoc_id"]);

// Open a LDAP connection
$ldap = new LDAP($ldap_host, $ldap_port, $ldap_version, $ldap_start_tls);

// Try to get user data on the LDAP
try {
    $data = $ldap->getDataForMattermost($ldap_base_dn, $ldap_filter, $ldap_bind_dn, $ldap_bind_pass, $ldap_search_attribute, $user);

    // Here is the patch for Mattermost 4.4 and newer. Gitlab has changed the JSON output of oauth service. Many data are not used by Mattermost, but there is a stack error if we delete them. That's the reason why date and many parameters are null or empty.
    $resp = array(
	"id" => $assoc_id,
	"name" => strlen($data['displayName']) > 0 ? $data['displayName'] : $data['cn'],
	"username" => $user,
	"state" => "active",
	"avatar_url" => "",
	"web_url" => "",
	"created_at" => "0000-00-00T00:00:00.000Z",
	"bio" => null,
	"location" => null,
	"skype" => "",
	"linkedin" => "",
	"twitter" => "",
	"website_url" => "",
	"organization" => null,
	"last_sign_in_at" => "0000-00-00T00:00:00.000Z",
	"confirmed_at" => "0000-00-00T00:00:00.000Z",
	"last_activity_on" => null,
	"email" => $data['mail'],
	"theme_id" => 1,
	"color_scheme_id" => 1,
	"projects_limit" => 100000,
	"current_sign_in_at" => "0000-00-00T00:00:00.000Z",
	"identities" => array(
	    array(
		"provider" => "ldapmain",
		"extern_uid" => $data['cn']
	    )
	),
	"can_create_group" => true,
	"can_create_project" => true,
	"two_factor_enabled" => false,
	"external" => false,
	"shared_runners_minutes_limit" => null
    );

    // Below is the old version, still consistent with Mattermost before version 4.4
    // $resp = array("name" => $data['cn'],"username" => $user,"id" => $assoc_id,"state" => "active","email" => $data['mail']);
} catch (Exception $e) {
    if ($e->getCode() == 404) {
	$resp = [
	    "error" => "User not found",
	    "message" => "$user is not in the group of authorized users."
	];
    } else {
	$resp = array(
	    "error" => "Impossible to get data",
	    "message" => $e->getMessage()
	);
    }
}

// send data or error message in JSON format
echo json_encode($resp);
