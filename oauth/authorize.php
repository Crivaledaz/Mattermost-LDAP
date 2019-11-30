<?php
session_start();

/**
 * @author Denis CLAVIER <clavierd at gmail dot com>
 * Adapted from Oauth2-server-php cookbook
 * @see http://bshaffer.github.io/oauth2-server-php-docs/cookbook/
 */

// include our OAuth2 Server object
require_once __DIR__.'/server.php';

$request = OAuth2\Request::createFromGlobals();
$response = new OAuth2\Response();

// validate the authorize request
if (!$server->validateAuthorizeRequest($request, $response)) {
    $response->send();
    die;
}

// if user is not yet authenticated, he is redirected.
if (!isset($_SESSION['uid']))
{
  //store the authorize request
  $explode_url=explode("/", strip_tags(trim($_SERVER['REQUEST_URI']))); 
  $_SESSION['auth_page']=end($explode_url);
  header('Location: index.php');
  exit();
}


// display an authorization form
if (empty($_POST)) {
  exit('
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./style.css">
		<title>Mattermost - LDAP Authorization</title>

		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
		integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
		<link href="https://fonts.googleapis.com/css?family=Roboto:300,400" rel="stylesheet">

	</head>

	<body>
		<div id="form-wrapper" style="text-align: center;">
			<div id="form_credentials">
				<h1>LDAP Authentication</h1>
				<div id="form_icon">
				<img src="./auth_icon.png" alt="authentication icon" >
				</div>
				<br>
				<h2>Authorize Mattermost to get the following data:</h2>
				<table>
					<tr>
						<td>
							&nbsp; <strong>Full Name</strong><br/> 
							&nbsp; <strong>E-mail</strong><br/>
							&nbsp; For the user <strong>' . $_SESSION['uid'] . '</strong><br/>
						</td>
					</tr>
				</table>
				<br>

				<form method="POST">
					<input type="submit" value="Authorize" name="authorized" id="input_accept" class="input_field">
					<input type="submit" value="Deny" name="authorized" id="input_deny" class="input_field">
				</form>
			</div>
		</div>
	</body>
</html>
  ');
}

// print the authorization code if the user has authorized your client
$is_authorized = ($_POST['authorized'] === 'Authorize');
$server->handleAuthorizeRequest($request, $response, $is_authorized,$_SESSION['uid']);

if ($is_authorized) 
{
  // This is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
  $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
  header('Location: ' . $response->getHttpHeader('Location'));
  exit();
}

// Send message in case of error
$response->send();
