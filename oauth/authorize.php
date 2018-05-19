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
    <meta charset="UTF-8" />
      <link rel="stylesheet" type="text/css" href="./style.css">
    <title>Authorisation Mattermost</title>
  </head>

  <body>


<center>
  <table background="../images/login.png" border="0" width="729" height="343" cellspacing="1" cellpadding="4">
    <tr>
      <td width="40%">&nbsp;</td>
      
      <td width="60%">
        <table border="0" width="100%">

          <tr>
            <td align="center">
              <div class="LoginTitle">Mattermost desires access to your LDAP data:</div>
        

            <form method="post">
            
                <table border="0" width="90%" cellpadding="1">
                    <tr>
                      <td colspan="2" align="left">
                        
                          <div class="messageLogin" align="center">
                            
                          </div>                         
                        &nbsp;
                      </td>
                    </tr>
                    <tr>
                      <td align="center" width="100%" class="LoginUsername">
                        Login as : <b>' . $_SESSION['uid'] . ' </b>
                      </td>
                    </tr>
                    <tr>
                      <td align="left" width="100%" class="LoginUsername">
                         
                        <br/>
                        Requested Data : <br/>
                        &nbsp; -> Username,<br/>
                        &nbsp; -> Full Name,<br/> 
                        &nbsp; -> Email
                      
                      </td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                      <td colspan="2" align="center"> <input type="submit" class="GreenButton" name="authorized" value="Authorize" > 
                      <input type="submit" class="GreenButton" name="authorized" value="Deny" > </td>

                    </tr>
                    
                    
                </table>
              </form>
              
          </td>
          </tr>
        </table>
      
      </td>
    </tr>
  </table>
</center>
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