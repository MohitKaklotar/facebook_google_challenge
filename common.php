<?php
ini_set('max_execution_time', 300);
session_start();
require_once __DIR__ . '/libs/Facebook/autoload.php';
require_once __DIR__.'/libs/google/vendor/autoload.php';

$app_id="your app id"; // get from your facebook app->settings 
$app_secret="your app secret"; // get from your facebook app->settings


	//fb_login_url is same url which is added into facebook app->settings.
	$fb_login_url = "your login url  where the response is come after succesfully login in facebook"; 
	$fb_logout_url = "your logout url";
	
?>
