<?php 
	require_once( 'common.php' );
	
	    //check wether session (fb access token session) is set or not
         if( !isset($_SESSION['fb_access_token']) && !isset($_GET['albumid']))
         {
             header("location:/index.php");
         }
 

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {


          function getClient()
            {
                    $client = new Google_Client();
                    $client->setAuthConfig('client_secrets.json'); //get client_secret.json from google console by enabling googledrive api
                    $client->setScopes(Google_Service_Drive::DRIVE_METADATA_READONLY);
                    $client->setAccessType('online');
                    $client->setScopes("https://www.googleapis.com/auth/drive.file");
                    $client->setAccessToken($_SESSION['access_token']);
                    return $client;
            }

            // Get the API client and construct the service object.

                $client = getClient();
                $service = new Google_Service_Drive($client);


                //create folder with facebook_username_album on google drive
                $fileMetadata = new Google_Service_Drive_DriveFile(array('name' => 'facebook_'.$_SESSION['user_name'].'_albums',
                                                                            'mimeType' => 'application/vnd.google-apps.folder'));
                $ParentFolder = $service->files->create($fileMetadata, array('fields' => 'id'));
                    
                
                $mydata=$_GET['albumid'];
                $mydata=explode(",",$mydata);
                $count=sizeof($mydata);
                 
                 for($i=0;$i<$count-1;$i++)
                 {
                     
                     $album_detail=explode("-",$mydata[$i]);
                     $count1=sizeof($album_detail);
                     $sub_dirs=$album_detail[0];
                          

                        //create sub folder with facebook album name within facebook_username_album on google drive                                                      
                        $folderId = $ParentFolder->id;
                        $fileMetadata = new Google_Service_Drive_DriveFile(array(
                            'name' => $sub_dirs,'mimeType' => 'application/vnd.google-apps.folder','parents' => array($folderId)
                            ));
                        $SubFolder = $service->files->create($fileMetadata, array('fields' => 'id'));
                
                                
                                
                         $fb = new Facebook\Facebook(['app_id' => $app_id, 'app_secret' => $app_secret,'default_graph_version' => 'v2.2',]);
                         $helper = $fb->getRedirectLoginHelper();
                        
                        $limitCnt=100;   
                        $offsetcnt=0;
                        //get first 100 images (only for loop until last result)
                        $response = $fb->get('/'.$album_detail[1].'/photos?limit='.$limitCnt,$_SESSION['fb_access_token']);
                        $pagesEdge = $response->getGraphEdge();
                         

                            // loop until next node found in responce  
                             do { 
                                   
                                    //each time get 100 images using offset and limit
                                    $responseImg = $fb->get('/'.$album_detail[1].'/photos?fields=source&limit='.$limitCnt.'&offset='.$offsetcnt,$_SESSION['fb_access_token']);
                                    $graphNodeImg = $responseImg->getGraphEdge();
                                 
                                     $resultImg = json_decode($graphNodeImg);
                                     
                                     
                                       
                                      foreach($resultImg as $mydata1)
                                      {
                                                 $url = $mydata1->source;
                                                 $img = $mydata1->id.".png";
                                                
                                                //put image inside sub folder 
                                                  $folderId = $SubFolder->id;
                                                  $fileMetadata = new Google_Service_Drive_DriveFile(array(
                                                        'name' => $img,'parents' => array($folderId)
                                                    ));
                                                     $content = file_get_contents($url);
                                                     $file = $service->files->create($fileMetadata, array(
                                                    'data' => $content,'mimeType' => 'image/jpeg',
                                                    'uploadType' => 'multipart','fields' => 'id'));
                                               

                                                
                                      }
                                         $offsetcnt=$offsetcnt+100;
                             } while ( $pagesEdge = $fb->next($pagesEdge));
                            
                            
                          
                          
                 }
                     echo "success";   
               
} else 
{
    echo "login_failed";    
}
?>