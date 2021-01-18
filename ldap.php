<?php

error_reporting(E_ALL);

###################################---Configuration---################################

// Ldap adress and port
$hostname = "ldap://localhost:389";

// LDAP version
$ldap_version = 3;

// User identifier - UID
$uid = "jdoe";

// Bind directory name
$bind_dn = "cn=butler,dc=example,dc=com";

// Bind password
$bind_pass = "readonly";

// Base directory name
$base = "ou=People,dc=example,dc=com";
######################################################################################


echo "<h3>LDAP : Test Center</h3>";
echo "Attempting to connect LDAP server ... <br />";
$ldap=ldap_connect($hostname);
ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, $ldap_version);

if ($ldap) {
	  echo "Successful connection ! <br />";
    echo "Authenticating with bind credentials ... <br />";
    $is_valid=ldap_bind($ldap,$bind_dn,$bind_pass);

    if ($is_valid) {
	    	echo "Successful authentication ! <br />";
	    	echo "Getting user informations ...<br />";
	    	$user_data=ldap_search($ldap, $base, "uid=" . $uid);

	    	if ($user_data) {
		    		echo "Data recovered with success ! <br />";
		    		echo "Extracting useful data : <br /><br />";
		    		$info_user = ldap_get_entries($ldap, $user_data);
		 			  for ($i=0; $i<$info_user["count"]; $i++) {
		       			echo "dn: " . $info_user[$i]["dn"] . "<br />";
		        		echo "cn: " . $info_user[$i]["cn"][0] . "<br />";
		        		echo "uid: " . $info_user[$i]["uid"][0] . "<br />";
		        		echo "email: " . $info_user[$i]["mail"][0] . "<br /><hr />";
		 				}
	 		  } else {
 					  echo "No data recovered ! <br /><br />";
 				}
    } else {
 		    echo "Identification has failed ... Check bind credentials<br /><br />";
    }
		echo "Closing LDAP connection.";
    ldap_close($ldap);
} else {
    echo "Impossible to connect to LDAP server !";
}
