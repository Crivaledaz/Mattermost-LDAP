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

// If user has clicked on "not me" link, disconnect him by cleaning PHP SESSION variables.
if (isset($_POST['disconnect'])) {
    $_SESSION=array();
}

// Validate the authorize request
if (!$server->validateAuthorizeRequest($request, $response)) {
    $response->send();
    die;
}

// If user is not yet authenticated, he is redirected.
if (!isset($_SESSION['uid']))
{
  // Store the authorize request
  $explode_url=explode("/", strip_tags(trim($_SERVER['REQUEST_URI'])));
  $_SESSION['auth_page']=end($explode_url);
  header('Location: access_token');
  exit();
}

// Check if user has already authorized oauth to share data with Mattermost. In this case, user should exist in 'user' table.
if ($server->userExists($_SESSION['uid'])) {
    // User had already authorized the client during a previous session.
    $is_authorized = true;
}
// Display an authorization form
else if (empty($_POST)) {
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
                <img src="./images/auth_icon.png" alt="authentication icon" >
                </div>
                <br>
                <h2>Authorize Mattermost to get the following data:</h2>
                <table>
                    <tr>
                        <td>
                            &nbsp; <strong>Full Name</strong><br/>
                            &nbsp; <strong>E-mail</strong><br/>
                        </td>
                    </tr>
                </table>
                <br/>
                Logged as : <strong>' . $_SESSION['uid'] . ' </strong> <button type="submit" class="link" name="disconnect" value="true" ><span>(not me ?)</span></button>
                <br/>
                <br/>

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
else {
    // Check if user has authorized to share his data with the client
    $is_authorized = ($_POST['authorized'] === 'Authorize');
}

// Print the authorization code if the user has authorized your client
$server->handleAuthorizeRequest($request, $response, $is_authorized,$_SESSION['uid']);

// Authentication process is terminated, session can be destroyed.
$_SESSION=array();

if ($is_authorized)
{
  // This is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
  $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
  header('Location: ' . $response->getHttpHeader('Location'));
  exit();
}

// Send message in case of error
$response->send();
