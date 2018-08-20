<?php
    require_once( 'common.php' );
    session_destroy(); 
    
    function removeDirectory($path) 
    {
        	$files = glob($path . '/*');
	        foreach ($files as $file) {
		    is_dir($file) ? removeDirectory($file) : unlink($file);
	        }
	    return;
    }

    //remove all zip file of downloaded albums from lib/albums_download
    removeDirectory('libs/albums_download');
    
    header("location:/index.php");
   
?>