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
	echo 'You must fill each field';
	echo 'Click <a href="./index.php">here</a> to come back to login page';
}
else
{
	// Check received data length (to prevent code injection) 
	if (strlen($_POST['user']) > 15)
 	{
  		echo 'Strange username ... Please try again';
		echo 'Click <a href="./index.php">here</a> to come back to login page';
    }
    elseif (strlen($_POST['password']) > 50 || strlen($_POST['password']) <= 7)
    {
    	echo 'Strange password ... Please try again';
		echo 'Click <a href="./index.php">here</a> to come back to login page';
    } 
    else
   	{
   		// Remove every html tag and useless space on username (to prevent XSS)
   	   	$user=strip_tags(trim($_POST['user']));

    	$user=$_POST['user'];
    	$password=$_POST['password'];

    	// Open a LDAP connection
    	$ldap = new LDAP($hostname,$port);
    	
    	//##################################################\\
    	//    /!\   Adapt here with your LDAP config   /!\  \\
    	//##################################################\\
    	
    	$rdn = 'uid=' . $user . ',ou=People,o=Company';

		/****************************************************/
		
		// Check user credential on LDAP
		if ($ldap->checkLogin($rdn,$password)) 
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
		echo "Authetification failed ... Check your username and password.<br />If error persist contact your administrator.<br /><br />";
		echo 'Click <a href="./index.php">here</a> to come back to login page';
		}
	}
}
