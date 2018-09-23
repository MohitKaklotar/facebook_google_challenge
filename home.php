<?php
  		require_once( 'common.php' );
?>

<!DOCTYPE html>
<html>
<title>Facebook Album Fatcher</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/libs/css/w3.css"  type='text/css'>
<link rel="stylesheet" href="/libs/css/bootstrap.min.css"  type='text/css'>
<script src="/libs/css/jquery.min.js"></script>
<script src="/libs/css/bootstrap.min.js"></script>
<link href='/libs/css/slider.css' rel='stylesheet' type='text/css'>
<link href='/libs/css/custom.css' rel='stylesheet' type='text/css'>
<link href='/libs/css/slider_extra.css' rel='stylesheet' type='text/css'>

<?php
            $fb = new Facebook\Facebook(['app_id' => $app_id, 'app_secret' => $app_secret,'default_graph_version' => 'v2.2',]);   
            $helper = $fb->getRedirectLoginHelper();
            $accessToken="";

            //get access token
            $accessToken = $helper->getAccessToken($fb_login_url);
            
            if($accessToken != "")    
            {
                //set access token to session 
                $_SESSION['fb_access_token'] = (string) $accessToken;
            }
         	if( isset($_SESSION['fb_access_token']) && $_SESSION['fb_access_token'] != "")
      		{
      		    $accessToken=$_SESSION['fb_access_token'];
      		}
      		else if( !isset($_SESSION['fb_access_token']) || $_SESSION['fb_access_token'] == "")
      		{
      		    ?> 
      		    <script>window.location = "/index.php";</script>
      		    <?php
      		}
      	
        //get user id and user name using access token 
        if($accessToken!="")
        {
            $user_details = "https://graph.facebook.com/me?access_token=" .$accessToken;
            $response = file_get_contents($user_details);
            $response = json_decode($response);
            $_SESSION['user_name']=$response->name;     
            $_SESSION['user_id']=$response->id;
        }

?>
<body>
<div class="">
<div class="w3-card-4" style="width:100%;">
<header class="w3-container" style="background-color:#3B5998;">
	<div style="float:left;" class="header_text"><h4 style="color:white;" class="head-font">Facebook Photo Fatcher</h4></div>
	<div style="float:right;" class="header_text">
		<div style="float:left;color:white;margin-top:8px;">
            <!-- get user profile picture -->
			<img src="<?php echo 'https://graph.facebook.com/'.$_SESSION['user_id'].'/picture';?>" alt="Avatar" style="height:40px;width:40px;  border-radius: 100%;">
			&nbsp;&nbsp;&nbsp;Hello <?php echo $_SESSION['user_name']; ?>
		</div>
		<div style="float:right;color:white;margin-top:5px;">
            <!--Logout -->
			<?php echo '<a href="'.$helper->getLogoutUrl($_SESSION['fb_access_token'], 'https://' . $_SERVER['HTTP_HOST'] . '/logout.php').'" ><i style="margin-left:20px;margin-right:20px;font-size:24px" class="fa">&#xf08b;</i></a>'; ?>
		</div>
	</div>
</header>
</div>

<center>
    
	<div style="width:90%;margin-top:20px;">
		 <div class="w3-row">
		 <div id="progress"></div>
		 <div id="progress1"></div>
					<div class="w3-col  w3-white w3-center box-with-left"  style="height: 500px;overflow: hidden;    position: relative;">
						<div id="container2">
							
					<?php		
			        	//get all album detail
			        	$all_album="";
			        	$single_album="";
                     
                        try {
                                    $response = $fb->get('/'.$_SESSION['user_id'].'/albums',$accessToken);
                            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                                    //return error if problem occurs
                                    echo 'Graph returned an error: ' . $e->getMessage();
                                    exit;
                            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                                    //return error if problem occurs
                                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                                    exit;
                            }
                            
                          $graphNode = $response->getGraphEdge();
                          $result = json_decode($graphNode);
                    
                            foreach($result as $mydata)
                            {
                                
                                $all_album.=$mydata->name."-".$mydata->id.",";
                                $album_name=$mydata->name;
                                $album_id=$mydata->id;
                              
                                            //get latest 1 photo of album to display as cover photo of album
                                              $response = $fb->get('/'.$mydata->id.'/photos?fields=source&limit=1',$accessToken);
                                       
                                                $graphNode = $response->getGraphEdge();
                                                $result = json_decode($graphNode);
                                                
                                                    foreach($result as $mydata)
                                                    {
                                                        $single_album=$album_name."-".$album_id;
                                                        
                                                          ?>
                                                              <div class="w3-card-4 box-wth" style="float:left;margin:10px;">
                                                                 
                                    							  <a onclick="myAlbumPhoto(<?php echo $album_id; ?>);">
                                    							  <div class="w3-display-container w3-text-white" data-toggle="modal" data-target="#myModal">
                                    								<img src="<?php echo $mydata->source; ?>" alt="Lights" style="width:100%;height:200px;">
                                    								<div class="w3-xlarge w3-display-bottomleft w3-padding">
                                    								    <div class="album_name">
                                    								             <b><?php echo $album_name; ?></b>
                                    								    </div>
                                    								</div>
                                    							  </div>
                                    							  </a>
                                    							  <div class="w3-row">
                                    								<div class="w3-col  w3-center" style="background-color:;width:20%;">
                                    									<label class="container">
                                    										<input type="checkbox" name="album_list[]" value="<?php echo $single_album; ?>">
                                    											<span class="checkmark"></span>
                                    									</label>
                                    								</div>
                                    								<div class="w3-col  w3-center" style="background-color:;width:40%;padding-top:5px;">
                                    								  <div class=" w3-center" style="">
                                    										<p style="color:white;">
                                    										<button class="w3-button w3-round-large down_single" style="background-color: #3B5998;" 
                                    										            onclick="download_album('<?php echo $album_name."-".$album_id.","; ?>')">
                                    											Download
                                    										</button>
                                    									  </p>
                                    									</div>
                                    								</div>
                                    								<div class="w3-col  w3-center" style="background-color:;width:40%;padding-top:5px;">
                                    								  <div class=" w3-center" style="">
                                    										<p style="color:white;">
                                    										<button class="w3-button w3-round-large move_single" style="background-color: #3B5998;"
                                    										    onclick="moveGoogle('<?php echo $album_name."-".$album_id.","; ?>')">
                                    											Move
                                    										</button>
                                    									  </p>
                                    									</div>
                                    								  
                                    								</div>
                                    							  </div>
                                    							</div>
                                						<?php
                                                         
                                                    }
                                   }
                                ?>
							
							
						</div>
					</div>
				  <div class="w3-col  w3-white w3-center box-with-right" >
						<div class="w3-card-4 " style="float:left;width:100%;margin:10px;">
							  
							  <div class="w3-row">
								<div class=" w3-center" style="margin-bottom:20px;margin-top:15px;">
									  <p style="color:white;">
										<button class="w3-button w3-round-large down_single" style="background-color: #3B5998;"  onclick="get_selected_album('download');">
											Download Selected
										</button>
									  </p>
								</div>
							
								<div class=" w3-center" style="margin-bottom:20px;">
								  <p style="color:white;">
										<button class="w3-button w3-round-large down_single" style="background-color: #3B5998;"   onclick="download_album('<?php echo $all_album; ?>');">
											Download All
										</button>
									  </p>
								</div>
								
								<div class=" w3-center" style="margin-bottom:20px;">
								  <p style="color:white;">
										<button class="w3-button w3-round-large move_single" style="background-color: #3B5998;" onclick="get_selected_album('move');">
											Move Selected
										</button>
									  </p>
								</div>
								
								<div class=" w3-center" style="margin-bottom:20px;">
								  <p style="color:white;">
									<button class="w3-button w3-round-large move_single" style="background-color: #3B5998;" onclick="moveGoogle('<?php echo $all_album; ?>');">
											Move All
										</button>
									  </p>
								</div>
								<div class=" w3-center" style="margin-bottom:20px;">
								  <p style="color:white;">
									<button class="w3-button w3-round-large" style="background-color: #3B5998;" onclick="upload_modal();">
											Upload
										</button>
									  </p>
								</div>
								
							  </div>
						</div>
				  </div>
		</div> 
		
	</div> 
	
  <!-- slider -->
 
        <div class="modal fade" id="myModal">
            <div class="modal-footer" style="margin:-12px !important;">
                <button type="button"  style="width:50px;float:right;background-color:#3b5998;color:white;" class="btn btn-default" data-dismiss="modal" onclick="modal_clear();">
                  &times;
                </button>
            </div>
            <div class="main" style="height:0%;">
                <div id="photo_slider"></div>
            </div>         
        </div>


<!--photo upload modal-->
<div class="modal fade popups" id="hint_brand" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content clearfix">
                <div class="modal-body login-box clearfix">
                    <div id="message" ></div>
                    <form id="uploadimage" method="post" action=""  enctype="multipart/form-data">
                        
                                <div style="height:70px;">
                                    <div  style="float:left;width:15%;">
                                        <img src="<?php echo 'https://graph.facebook.com/'.$_SESSION['user_id'].'/picture';?>" style="width:70px;height:70px;" >
                                    </div>
                                    <div  style="float:right;width:85%;">
                                        <textarea class="form-control upostTextarea" placeholder="What's on your mind" name="msg" 
                                        style="  outline: none !important; border: 0; outline: 0;background: transparent;border-bottom: 1px solid #D3D3D3;"></textarea>
                                    </div>
                                </div>   
                           
                                 <div style="width:100%;margin-top:15px;margin-bottom:15px;">
                                     <div style="float:left;width:25%;">
                                                     <div class="fileUpload btn btn-primary" style="font-size:14px !important;padding:5px; !important;background-color: #3B5998;width:125px !important;margin-left:-5px;">
                                                        <span>Select Image</span>
                                                        <input type="file" class="upload" name="upload_image[]" id="files" accept="image/*" multiple />
                                                    </div>
                                           
                                     </div>
                                     <div id="result" style="float:left;width:75%;">
                                     </div>
                                </div>
                  
                                <div style="height:10px;margin-top:15px;" id="post_button">
								<input type="submit" value="Post" class="fileUpload btn btn-primary" style="font-size:14px !important;padding:5px !important;color:white;float:right;background-color: #3B5998;width:100px !important;" onclick="" disabled ></input>
							
							    </div>
                   
                    </form>
                </div>
            </div>
    </div>
</div>

</center>
</div>

<!--Download click modal-->
<div class="modal fade" id="myModal1" style="height:100px;width:400px;margin-left:500px;margin-top:200px;background-color:whitesmoke;">
    <div class="main" style="background-color:whitesmoke;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" style="color:black;">&times;</button>
            <a href="javascript:void(0);" onclick="downAlbum();"><h4>Click On Link to Download album</h4></a>
        </div>
        <input type="text" id="dwname" style="display:none;"/>
    </div>
</div>
     
<script>

//ajax request for upload photo to facebook on submit of photo upload button
$(document).ready(function (e) {
            $("#uploadimage").on('submit',(function(e) {
                e.preventDefault();
                    $("#message").empty();
                    $("#post_button").html('<span style="font-weight:900;color:#3B5998;font-size:14px !important;float:right;"><i class="fa fa-spinner fa-spin" ></i>Posting..</span>');    
                    
                    
                  
                        $.ajax({
                        url: "/upload.php", 
                        type: "POST",       
                        data: new FormData(this), 
                        contentType: false,      
                        cache: false,             
                        processData:false, 
                            success: function(data)   // A function to be called if request succeeds
                            {
                                if(data == "uploaded")
                                {
                                     location.reload(); 
                                }
                                else
                                {
                                    $("#post_button").html('<input type="submit" value="Post" class="fileUpload btn btn-primary" style="font-size:14px !important;padding:5px !important;color:white;float:right;background-color: #3B5998;width:100px !important;" onclick=""  ></input>'); 
                                    $("#message").html(data);
                                }
                            }
                        });
            }));
});

function upload_modal()
{
    $("#hint_brand").modal("show");
    
}    

//download album after click on link
function downAlbum()
{
    var val=document.getElementById("dwname").value;
    document.location="/"+val;
    $('#myModal1').modal('hide');
    
}

function get_selected_album(action)
{
    var checkboxes = document.getElementsByName('album_list[]');
    var vals = "";
        for (var i=0, n=checkboxes.length;i<n;i++) 
        {
            if (checkboxes[i].checked) 
            {
                vals += checkboxes[i].value+",";
            }
        }
         if(vals=="")
         {
             alert("Please Select Album");
         }
         else if(action == "download")
         {
            download_album(vals);    
         }
         else if(action == "move")
         {
            moveGoogle(vals);    
         }
}

function progrebar(cnt,status)
{
        var html="";
        html+=status+'<div class="container">';
        html+='<div class="progress">';
        html+='<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width:'+cnt+'%">';
        html+=cnt+'%';
        html+='</div></div></div>';
        
        if(status=="Downloading...")
        {
                $("#progress").html(html);
        }
        else
        {
            $("#progress1").html(html);
        }
}

//download album with progressbar
function download_album(albumDetail)
{
    var cnt=0;
    progrebar(cnt,"Downloading...");
    $('.down_single').prop('disabled', true);
    
    var timer= setInterval(function(){ 
          if(cnt<100){   cnt++; }
          progrebar(cnt,"Downloading...");
     }, 500);
     
    $("#progress").show();
    
        $.ajax({
                  url: "/zip.php?albumid="+albumDetail, 
                  timeout: 300000,
                  success: function(result)
                  {
                      $('.down_single').prop('disabled', false);
                      $("#dwname").val(result);
                      $("#myModal1").modal('show');
                      clearInterval(timer);
                      progrebar(100,"Downloading...");
                      setTimeout(function(){ $("#progress").hide(); }, 1000);
                      return;
                }
            });
   
}
//move album with progressbar
function moveGoogle(albumDetail)
{
    
    <?php
    if(!isset($_SESSION['access_token']))
    {
        ?>
        document.location="/google_login.php";
        <?php
    }
    else
    {
        ?>

      $('.move_single').prop('disabled', true);
      var cnt=0;
      progrebar(cnt,"Moving...");
      var timer= setInterval(function(){ 
              if(cnt<100){   cnt++; }
                    progrebar(cnt,"Moving...");
             }, 500);
             
         $("#progress1").show();
    
    
         $.ajax({
                      url: "/google_move.php?albumid="+albumDetail, 
                      timeout: 300000,
                             success: function(result)
                                {
                                    $('.move_single').prop('disabled', false);
                                    
                                    if(result == "login_failed")
                                    {
                                        document.location="/google_login.php";
                                    }
                                    else
                                    {
                                        clearInterval(timer);
                                        progrebar(100,"Moving...");
                                        setTimeout(function(){ $("#progress1").hide(); }, 1000);
                                        alert("Album Moved to Google Suceefully");
                                    }
                                }     
                });

         <?php
    }
        ?>
    
}

function modal_clear()
{
     document.getElementById("photo_slider").innerHTML = ""; 
}

//display slider on click album photo
function myAlbumPhoto(albumid)
{
      var prepro="<center><div><i class='fa fa-spinner fa-spin' style='color:#3b5998;font-size:60px;margin-top:250px;'></i></div></center>";    
      $("#photo_slider").append(prepro);
      var html1="";
    
         $.ajax({
                    url: "/album_display.php?albumid="+albumid, 
                     success: function(result)
                        {
                            document.getElementById("photo_slider").innerHTML = ""; 
                            var obj = JSON.parse(result);
                            var count=Object.keys(obj).length;
                            
                                if(result !== null && result !== '') 
                                {
                                    
                                   html1+='<div class="slider">';
                                      html1+='<div class="slide_viewer">';
                                            html1+='<div class="slide_group">';
                                             
                                              for(var i=0;i<count;i++)
                                                 {   
                                                
                                                    html1+='<div class="slide">';
                                                        html1+='<div class="layer">';
                                                            html1+='<div >';
                                                                html1+='<img src="'+obj[i].source+'" style="object-position: 70% 30%; object-fit: cover;opacity: 1 !important;width: 100%;height: 600px; ">';
                                                                html1+="<div class='w3-display-bottomleft albumtotal' ><div class='album_displaytotal'>";
                                                                html1+= i+1+" of "+count;
                                                                html1+="</div></div>";
                                                            html1+='</div>';
                                                        html1+='</div>';
                                                    html1+='</div>';
                                                   
                                                 }
                                                
                                            html1+='</div>';
                                        html1+='</div>';
                                    html1+='</div>';
                            
                                    html1+='<div class="directional_nav">';
                                          html1+='<div class="previous_btn" title="Previous" style="margin-left:45px !important;">';
                                        		html1+='<i class="fa fa-arrow-circle-left" style="font-size:48px;color:#3b5998;font-weight:900;"></i>';
                                          html1+='</div>';
                                          html1+='<div class="next_btn" title="Next" style="margin-right:50px !important;">';
                                            html1+='<i class="fa fa-arrow-circle-right" style="font-size:48px;color:#3b5998;font-weight:900;"></i>';
                                          html1+='</div>';
                                    html1+='</div>';
                                    
                                    $("#photo_slider").append(html1);
                                    slider_data();
                                    $('#myModal').modal('show');
                                }
                        }
                });
}

function slider_data()
{
                     $('.slider').each(function() {
                          var $this = $(this);
                          var $group = $this.find('.slide_group');
                          var $slides = $this.find('.slide');
                          var bulletArray = [];
                          var currentIndex = 0;
                          var timeout;
                          
                          function move(newIndex) {
                            var animateLeft, slideLeft;
                            
                            advance();
                            
                            if ($group.is(':animated') || currentIndex === newIndex) {
                              return;
                            }
                            
                            bulletArray[currentIndex].removeClass('active');
                            bulletArray[newIndex].addClass('active');
                            
                            if (newIndex > currentIndex) {
                              slideLeft = '100%';
                              animateLeft = '-100%';
                            } else {
                              slideLeft = '-100%';
                              animateLeft = '100%';
                            }
                            
                            $slides.eq(newIndex).css({
                              display: 'block',
                              left: slideLeft
                            });
                            $group.animate({
                              left: animateLeft
                            }, function() {
                              $slides.eq(currentIndex).css({
                                display: 'none'
                              });
                              $slides.eq(newIndex).css({
                                left: 0
                              });
                              $group.css({
                                left: 0
                              });
                              currentIndex = newIndex;
                            });
                          }
                          
                          function advance() {
                            clearTimeout(timeout);
                            timeout = setTimeout(function() {
                              if (currentIndex < ($slides.length - 1)) {
                                move(currentIndex + 1);
                              } else {
                                move(0);
                              }
                            }, 4000);
                          }
                          
                          $('.next_btn').on('click', function() {
                            if (currentIndex < ($slides.length - 1)) {
                              move(currentIndex + 1);
                            } else {
                              move(0);
                            }
                          });
                          
                          $('.previous_btn').on('click', function() {
                            if (currentIndex !== 0) {
                              move(currentIndex - 1);
                            } else {
                              move(3);
                            }
                          });
                          
                          $.each($slides, function(index) {
                            var $button = $('<a class="slide_btn">&bull;</a>');
                            
                            if (index === currentIndex) {
                              $button.addClass('active');
                            }
                            $button.on('click', function() {
                              move(index);
                            }).appendTo('.slide_buttons');
                            bulletArray.push($button);
                          });
                          
                          advance();
                        });
}

//after selecting photo display it on modal
window.onload = function()
{
        var filesInput = document.getElementById("files");
       
        filesInput.addEventListener("change", function(event)
        {
             document.getElementById("result").innerHTML="";
            var files = event.target.files; //FileList object
            
            $("#message").html("");
            for(var i = 0; i< files.length; i++)
            {
                var file = files[i];
                
                //Only pics
                if(!file.type.match('image'))
                  continue;
                

                // display each photo
                var picReader = new FileReader();
                picReader.addEventListener("load",function(event)
                {
                    var picFile = event.target;
                    $("#result").append("<div  style='height:100px;float:left;width:25%;'><img class='thumbnail' style='width:100px;height:100px;' src='" + picFile.result + "'" + "/></div>");
                    
                });
                
                 //Read the image
                picReader.readAsDataURL(file);
                
                $(':input[type="submit"]').prop('disabled', false);
            }   
            if(files.length>5)
            {
                 $("#message").html("Maximum 5 images...");
                 $(':input[type="submit"]').prop('disabled', true);
            }                            
           
        });
   
}
</script>
 
</body>
</html>
