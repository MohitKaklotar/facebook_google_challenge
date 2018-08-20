<?php
  		 require_once( 'common.php' );
  		 
  		 //check wether session (fb access token session) is set or not
  		 if( !isset($_SESSION['fb_access_token']) && !isset($_GET['albumid']))
  		 {
  		     header("location:/index.php");
  		 }
  		 $accessToken=$_SESSION['fb_access_token'];
  		
  		      $limitCnt=100;
              $offsetcnt=0;
  		     $fb = new Facebook\Facebook(['app_id' => $app_id, 'app_secret' => $app_secret,'default_graph_version' => 'v2.2',]);
             
             //get first 100 images (only for loop until last result)
             $response = $fb->get('/'.$_GET['albumid'].'/photos?fields=source&limit='.$limitCnt,$accessToken);
            
              $pagesEdge = $response->getGraphEdge();
             
              
              $all_img = array();
                // loop until next node found in responce
                do { 
                     
                     //each time get 100 images using offset and limit
                        $responseImg = $fb->get('/'.$_GET['albumid'].'/photos?fields=source&limit='.$limitCnt.'&offset='.$offsetcnt,$accessToken);
                        $graphNodeImg = $responseImg->getGraphEdge();
                     
                         $resultImg = json_decode($graphNodeImg);

                         foreach($resultImg as $mydata)
                         {
                           
                             $myObj = new stdClass();
                             $myObj->source = $mydata->source;
                             $myObj->id = $mydata->id;
                             array_push($all_img,$myObj);
                         }
                      $offsetcnt=$offsetcnt+100;
                } while ( $pagesEdge = $fb->next($pagesEdge));
                 
                 //array to json
                 $myJSON = json_encode($all_img);
                 echo $myJSON;
                                          
  ?>