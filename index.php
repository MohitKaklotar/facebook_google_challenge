<?php
  		require_once( 'common.php' );
  		
  		if( isset($_SESSION['fb_access_token']) && $_SESSION['fb_access_token'] != "")
      		{
      		   header("location:/home.php");
      		}
  ?>

<!DOCTYPE html>
<html>
<title>Facebook Album Fatcher</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link href='/libs/css/custom1.css' rel='stylesheet' type='text/css'>

<body>
<div class="">
<div class="w3-card-4" style="width:100%;">
<header class="w3-container" style="background-color:#3B5998;">
<h4 style="color:white;" class="head-font">Facebook Photo Fatcher</h4>
</header>
</div>
<center>
	<div class="custom-css w3-panel w3-hover-shadow w3-card w3-display-container" style="background-color:#DCDCDC;margin-top:100px;">
		<p>Using this App you can watch all facebook photo album wise, on click any album you can see all photo 
		inside that perticuler album with slider, also can download and move photo to google drive </p>
		
		<?php
              $fb = new Facebook\Facebook([
              'app_id' => $app_id, // Replace {app-id} with your app id in common.php file
              'app_secret' => $app_secret, // Replace {app-secret} with your app secret in common.php file
              'default_graph_version' => 'v2.2',
              ]);
        
                $helper = $fb->getRedirectLoginHelper();
                    $permissions = ['user_photos']; //  permissions to fatch user photo
                $loginUrl = $helper->getLoginUrl($fb_login_url, $permissions);
                
                echo '<a href="' . htmlspecialchars($loginUrl) . '" class="fa fa-facebook">Login with Facebook</a>';
        ?>
  
	</div> 
</center>
</div>
</body>
</html>
