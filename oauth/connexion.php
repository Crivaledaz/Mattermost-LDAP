<?php
session_start();
/**
 * @author Denis CLAVIER <clavierd at gmail dot com>
 */

// include our LDAP object
require_once __DIR__.'/LDAP/LDAP.php';
require_once __DIR__.'/LDAP/config_ldap.php';


// Verify all fields have been filled 
if (empty($_POST['user']) || empty($_POST['password'])) 
{
	echo 'Please fill in your Username and Password<br /><br />';
	echo 'Click <a href="./index.php">here</a> to come back to login page';
}
else
{
	// Check received data length (to prevent code injection) 
	if (strlen($_POST['user']) > 15)
 	{
  		echo 'Username has incorrect format ... Please try again<br /><br />';
		echo 'Click <a href="./index.php">here</a> to come back to login page';
    }
    elseif (strlen($_POST['password']) > 50 || strlen($_POST['password']) <= 7)
    {
    	echo 'Password has incorrect format ... Please try again<br /><br />';
		echo 'Click <a href="./index.php">here</a> to come back to login page';
    } 
    else
   	{
   		// Remove every html tag and useless space on username (to prevent XSS)
   	   	$user=strip_tags(trim($_POST['user']));

    	$user=$_POST['user'];
    	$password=$_POST['password'];

    	// Open a LDAP connection
    	$ldap = new LDAP($hostname,$port,$ldap_version);
		
		// Check user credential on LDAP
		try{
			$authenticated = $ldap->checkLogin($user,$password,$search_attribute,$filter,$base,$bind_dn,$bind_pass);
		}
		catch (Exception $e)
		{
			$resp = json_encode(array("error" => "Impossible to get data", "message" => $e->getMessage()));
			$authenticated = false;
		}

		// If user is authenticated
		if ($authenticated) 
		{
		    $_SESSION['uid']=$user;

		    // If user came here with an autorize request, redirect him to the authorize page. Else prompt a simple message.
		    if (isset($_SESSION['auth_page']))
		    {
		    	$auth_page=$_SESSION['auth_page'];
		    	header('Location: ' . $auth_page);
 				exit();
		    }
		 	else 
		 	{
		 		echo "Congratulation you are authenticated ! <br /><br /> However there is nothing to do here ...";
			}
	    }
	    // check login on LDAP has failed. Login and password were invalid or LDAP is unreachable
		else 
		{
		echo "Authentication failed ... Check your username and password.<br />If error persist contact your administrator.<br /><br />";
		echo 'Click <a href="./index.php">here</a> to come back to login page';
		echo '<br /><br /><br />' . $resp;
		}
	}
}
