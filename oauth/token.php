 <?php
/**
 * Adapted from Oauth2-server-php cookbook
 * @see http://bshaffer.github.io/oauth2-server-php-docs/cookbook/
 */

// include our OAuth2 Server object
require_once __DIR__.'/server.php';

// Handle a request for an OAuth2.0 Access Token and send the response to the client
error_log("token.php \$_POST = " . json_encode($_POST));
// The Mattermost server seems to be returning bare http urls, even though there is no http url in the config.json

if (substr($_POST["redirect_uri"],0,5) == "http:") {
    $_POST["redirect_uri"] = "https" . substr($_POST["redirect_uri"],4);
}

$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
?>
