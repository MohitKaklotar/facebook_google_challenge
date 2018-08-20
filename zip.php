<?php
       
         require_once( 'common.php' );
    
    //check wether session is set or not
     if( !isset($_SESSION['fb_access_token']) && !isset($_GET['albumid']))
         {
             header("location:/index.php");
         }
    
        //check for download directory,if not available create it
		if ( !file_exists( "libs/albums_download" ) ) {
			mkdir("libs/albums_download", 0777);
		}  

    $fb = new Facebook\Facebook(['app_id' => $app_id, 'app_secret' => $app_secret,'default_graph_version' => 'v2.2',]);
    $helper = $fb->getRedirectLoginHelper();
            
            
                $accessToken=$_SESSION['fb_access_token'];
                $main_dir="facebook_album_";
                $dir_path="libs/albums_download/".$main_dir.rand(10,100000);

                //create folder 
                mkdir($dir_path);
            
            
                $mydata=$_GET['albumid'];
                $mydata=explode(",",$mydata);
                $count=sizeof($mydata);
                
                $cnt=0;
                 for($i=0;$i<$count-1;$i++)
                 {
                     
                     $album_detail=explode("-",$mydata[$i]);
                     $count1=sizeof($album_detail);
                    
                     
                          $sub_dirs=$album_detail[0];
                          $final_path=$dir_path."/".$sub_dirs;

                          //create subfolder
                          mkdir($final_path);
                           
                           
                              $limitCnt=100;
                              $offsetcnt=0;
                            
                            //get first 100 images (only for loop until last result)
                              $response = $fb->get('/'.$album_detail[1].'/photos?fields=source&limit='.$limitCnt,$accessToken);
                              $pagesEdge = $response->getGraphEdge();
                            
                                 
                                // loop until next node found in responce                            
                                do { 
                                    
                                        //each time get 100 images using offset and limit
                                        $responseImg = $fb->get('/'.$album_detail[1].'/photos?fields=source&limit='.$limitCnt.'&offset='.$offsetcnt,$accessToken);
                                        $graphNodeImg = $responseImg->getGraphEdge();
                                     
                                         $resultImg = json_decode($graphNodeImg);
                
                                         foreach($resultImg as $mydatas)
                                         {
                                             $url = $mydatas->source;
                                              $img = $mydatas->id.".png";

                                              //put image according to albums
                                             file_put_contents($final_path."/".$img, file_get_contents($url));
                                         }
                                      $offsetcnt=$offsetcnt+100;
                                } while ( $pagesEdge = $fb->next($pagesEdge));
                                 
                               
                        
                                     
                 }
                   
                 $zip_name=$dir_path.".zip";

                 //create zip of all album
                 Zip($dir_path,$zip_name);
                
                 removeDirectory($dir_path);
                 echo $dir_path.".zip";
                 exit();
             
             
               
                
           
 
 
        function removeDirectory($dir_path) 
        {
            	$files = glob($dir_path . '/*');
            	foreach ($files as $file) 
                {
            		is_dir($file) ? removeDirectory($file) : unlink($file);
            	}
            	rmdir($dir_path);
            
        }
             
            function Zip($source, $destination)
            {
                if (!extension_loaded('zip') || !file_exists($source)) {
                    return false;
                }
                  
            
                $zip = new ZipArchive();
                if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
                    return false;
                }
            
                $source = str_replace('\\', '/', realpath($source));
            
                //check wether source is directory or not
                if (is_dir($source) === true)
                {
                    //Recursivly get all file from all sub folders
                    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
            
                    foreach ($files as $file)
                    {
                        $file = str_replace('\\', '/', $file);
            
                        // Ignore "." and ".." folders
                        if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                            continue;
            
                        $file = realpath($file);
            
                        if (is_dir($file) === true)
                        {
                            $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                        }
                        else if (is_file($file) === true)
                        {
                            $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                        }
                    }
                }
                else if (is_file($source) === true)
                {
                    $zip->addFromString(basename($source), file_get_contents($source));
                }
            
                return $zip->close();
            }
?>
    