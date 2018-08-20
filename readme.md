Facebook Album Downloader and upload to Google Drive
=====================================

Working Demo :  <a href="http://coreless-schedules.000webhostapp.com/">Facebook Albums Fatcher</a> 

Working:

PART 1 :

User Login using Facebook
Ask user to give permission to access of photos.
Application fatches all Albums which is added by user or in which user is tagged.


PART 2 :

Albums are displayed with a Thumbnail, Album Name.
When a user clicks on Album cover-photo, all photos for that album are displayed in full screen slideshow.

A "Download" button is displayed for each album.
When user clicks on "Download" button, jquery(Ajax) processes PHP script to collect photos for that album, Zip them and prompts "Click On Link to Download album" Link to user for download.

An checkbox is displayed for each album.
A "Download Selected" button is displayed at right side.
When user clicks on "Download Selected" button, jquery(Ajax) processes PHP script to collect photos for all checked albums, Zip them and prompts "Click On Link to Download album" Link to user for download.

A "Download All" button is displayed at top.
When user clicks on "Download All" link, jquery(Ajax) processes PHP script to collect photos for all albums, Zip them and prompts "Click On Link to Download album" Link to user for download.

All the time while albums are downloading and processed into zip, progress bar are visible with percentage


PART 3 :

NOTE : At first time if user is not login to google account then it sends to login page and asks to grant access from user. 

A "Move" button is displayed for each album.
When user clicks on "Move" button, jquery(Ajax) processes PHP script to collect photos for that album and upload into  Google Drive.

An checkbox is displayed for each album.
A "Move Selected" button is displayed at right side.
When user clicks on "Move Selected" button, jquery(Ajax) processes PHP script to collect photos for all checked albums and upload into Google Drive.

A "Move All" button is displayed right side.
When user clicks on "Move All" button, jquery(Ajax) processes PHP script to collect photos for all albums and upload into Google Drive

All the time while albums are processed to move, progress bar are visible with percentage.


Importance

An responsive application which is works on Desktop, Tablets and mobile.
Mobile/Tablet users having move and download button available at bottom.
Album image width is managed according to device
There will be one column of album in mobile , Tablets will have 2 column of album image



Platforms:
PHP


Library Used:
==========================================================
Facebook PHP SDK
----------------------
The Facebook SDK for PHP provides developers with a modern, native library for accessing the Graph API and 
taking advantage of Facebook Login. Usually this means you're developing with PHP for a Facebook Canvas app, 
building your own website, or adding server-side functionality to an app.
More information and examples: <a href="https://developers.facebook.com/docs/reference/php/4.0.0/">https://developers.facebook.com/docs/reference/php/4.0.0</a>

Facebook PHP SDK :- <a href="https://github.com/facebook/php-graph-sdk">https://github.com/facebook/php-graph-sdk</a>


Google PHP SDK for login and drive access
----------------------
More information and examples: <a href="https://developers.google.com/drive/">https://developers.google.com/drive/</a>

Google PHP SDK :- <a href="https://github.com/google/google-api-php-client">https://github.com/google/google-api-php-client</a>


Twitter Bootstrap
----------------------
Bootstrap is the most popular HTML, CSS, and JS framework for developing responsive, mobile first projects on the web.
More information and examples: <a href="http://getbootstrap.com">http://getbootstrap.com/</a>


Scripting Languages:
Jquery
Ajax


Styling:
Css


How To use 
================================================


Facebook 
-----------------------------------

=> login to your faacebook account -> From left menu in home screen select Manage app
								   ->Add a New App
								   -> Give Name Of app an create new app id.
								   -> provide email adress
								   -> click on create app ID			
															      
=> from add a product, select facebook login -> click on set up link
=> from left side menu select facebook login -> setting
											 -> specify Valid OAuth Redirect URIs (facebook will redirect user to this page after   successfully login with facebook)

=> from left side menu select setting -> Basic -> App Domains -> enter app domain name

=> you will find your app id and app secret in setting -> basic

=> NOTE: App only work from developer credential until you approve your app to facebook.
	   : You can use test creadential provided by facebook to test your app
	     To create test creadential goto Roles -> Test Users -> click on add Button 



Google
----------------------------------

=> Go on  <a href="https://console.developers.google.com/">https://console.developers.google.com/</a>
=> select Credentials from left side -> select OAuth consent screen
									 -> Enter Email Address 	
									 -> Enter Product name shown to users 
									 -> save	
									 
=> select Credentials from left side -> Create Credentials -> select OAuth client ID from option
														   -> select Web application from application type
														   -> give name
														   -> provide Authorized redirect URIs
														   		 ->This is the path in your application that users are redirected to after they have authenticated with Google.  
														   	-> save
														   	-> now client_secret.json is available, you can download it and put in your webserver



how to use this code
================================================


=> Download this app from github
=> put this in root directory(Wamp => www, xampp => htdocs)
=> unzip it.
=> go to common.php
	Set:
		$fb_app_id = 'your-fb-app-key';
		$fb_secret_id = 'your-fb-app-secret-key';
	
		//fb_login_url is same url which is added in facebook app->settings.
		$fb_login_url = 'your login url or index url where the response is come'; 
		$fb_logout_url = 'your logout url';

=> put client_secret.json file of google credentials on root directory or any other location and provide location in goole_login.php | 	google_move.php file 
=> Run the index.php page and have fun
