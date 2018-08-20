<?php
  		require_once( 'common.php' );


$client = new Google_Client();
//get client_secret.json from developer google console by enabling googledrive api and put file on server
$client->setAuthConfigFile('client_secrets.json'); 
$client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '/google_login.php'); //set login url same as in developer google console
$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
$client->setScopes("https://www.googleapis.com/auth/drive");

if (! isset($_GET['code'])) 
{
      $auth_url = $client->createAuthUrl();
      header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
}
else 
{
      $client->authenticate($_GET['code']);
      $_SESSION['access_token'] = $client->getAccessToken();
      $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/home.php'; //redirect on this page after successfully login with google
      header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

?>