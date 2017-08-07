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
  $_SESSION['auth_page']=end(explode("/", strip_tags(trim($_SERVER['REQUEST_URI']))));
  header('Location: index.php');
  exit();
}


// display an authorization form
if (empty($_POST)) {
  exit('
<form method="post">
  <label>Mattermost wants to access your LDAP informations (ID, complete name, mail) </label><br />
  <input type="submit" name="authorized" value="Authorize">
  <input type="submit" name="authorized" value="Deny">
</form>');
}

// print the authorization code if the user has authorized your client
$is_authorized = ($_POST['authorized'] === 'Authorize');
$server->handleAuthorizeRequest($request, $response, $is_authorized,$_SESSION['uid']);

if ($is_authorized) 
{
  // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
  $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
  header('Location: ' . $response->getHttpHeader('Location'));
  exit();
}

// Send message in case of error
$response->send();