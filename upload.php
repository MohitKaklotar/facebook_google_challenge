<?php
require_once( 'common.php' );
if(empty($_SESSION['fb_access_token']))
     {
         header("location:/index.php");
     }
if(isset($_FILES["upload_image"]))
{
  
    $a=array();
    $allImage=array();
    $all_images1=0;
    $all_images2=0;
    for ($i = 0; $i < count($_FILES['upload_image']['name']); $i++) 
        {
           
            $all_images1+=1;
               
                //check for image file size | must be less than 1 MB
                if (($_FILES["upload_image"]["size"][$i] < 1048576)) {
               
                        $all_images2+=1;
                          
                }
                else
                {
                         echo "image size should not be more than 1 MB";
                         exit();
                }
        }
     
      
        if($all_images1 ==  $all_images2)
        {
                //upload each image on facebook without publicing them
                //image will be on facebook server for 24 hour after upload, after 24 hour it will automatically delete by facebook
                for ($i = 0; $i < count($_FILES['upload_image']['name']); $i++) 
                {
                            $extension = pathinfo($_FILES["upload_image"]["name"][$i], PATHINFO_EXTENSION);
                            $dir="libs/albums_download/".mt_rand().".".$extension;
                            $sourcePath = $_FILES['upload_image']['tmp_name'][$i]; 
                            move_uploaded_file($sourcePath,$dir) ; 
                            
                            $allImage[$i]=$dir;
                            
                            $ch = curl_init();
                            $curlConfig = array(
                                CURLOPT_URL            => "https://graph.facebook.com/v2.11/me/photos",
                                CURLOPT_POST           => true,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_POSTFIELDS     => array(
                                    'published' => 'false',
                                    'access_token' => $_SESSION['fb_access_token'],
                                    'url' => "http://".$_SERVER['HTTP_HOST']."/".$dir,
                                )
                            );
                            curl_setopt_array($ch, $curlConfig);
                            $result1 = curl_exec($ch);
                            curl_close($ch);
                            
                            //return unique id for each image
                             $result = json_decode($result1);

                             //push ids to array
                             array_push($a,$result->id);
                            
                           
                }
                   
                  
                    
                    
                    
                    $ch = curl_init();
                    $curlConfig = array(
                        CURLOPT_URL            => "https://graph.facebook.com/v2.11/me/feed",
                        CURLOPT_POST           => true,
                        CURLOPT_POSTFIELDS     => array(
                                                'message' => $_POST['msg'],
                                                'access_token' => $_SESSION['fb_access_token'],
                                            )
                    );
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    
                    $cnt=count($a);
                   //extract uploaded image ids from array and publish them at once
                    for ($i = 0; $i < $cnt; $i++) 
                    {
                            $curlConfig[CURLOPT_POSTFIELDS]["attached_media[$i]"]="{media_fbid:$a[$i]}";
                        
                    }
                    
                    curl_setopt_array($ch, $curlConfig);
                    $final = curl_exec($ch);
                     curl_close($ch);
                     
                     
                    //remove image from our server
                      for ($i = 0; $i <  count($allImage); $i++) 
                        {
                            unlink($allImage[$i]);
                        }
                     
                     echo "uploaded"; 
                    
        }
        
}
?>