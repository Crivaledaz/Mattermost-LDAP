 <?php
/**
 * Adapted from Oauth2-server-php cookbook
 * @see http://bshaffer.github.io/oauth2-server-php-docs/cookbook/
 */

// include our OAuth2 Server object
require_once __DIR__.'/server.php';
require_once __DIR__.'/config.php';


error_log("token.php \$_POST = " . json_encode($_POST));
/*

  The Mattermost server seems to be returning bare http urls, even
  though there is no http url in the config.json.  If we are using
  https we need to modify them.

*/
$redirect_url_scheme = substr($_POST["redirect_uri"], 0, 5);

if ($url_scheme == "https" && $redirect_url_scheme == "http:") {
    $_POST["redirect_uri"] = "https" . substr($_POST["redirect_uri"], 4);
}

// Handle a request for an OAuth2.0 Access Token and send the response to the client
$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
?>
