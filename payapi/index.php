<?php 
	include('languages/lang_config.php');
	include('admin/config/apply.php');
	include('includes/functions.php');
	
	if(get_setting('is_maintenance')==1){
		header('LOCATION: error');
	}else{
		header('LOCATION: admin');
	}
	// include('includes/header.php');
	 //include('includes/body.php');
	// include('includes/footer.php');
?>
