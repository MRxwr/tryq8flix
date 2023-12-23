<?php 
	if(!isset($_SESSION['user']))
	{
		header('location:'.SITEURL.'admin/login.php');
	}else{
		 $user = $_SESSION['user'];
		 $tbl_name = 'tbl_users';
		 $where = "username='$user'";
		 $query = $obj->select_data($tbl_name,$where);
		 $res = $obj->execute_query($conn,$query);
		 if($res==true)
		 {
			 $count_rows = $obj->num_rows($res);
			 if($count_rows==1)
			 {
				 $row = $obj->fetch_data($res);
				 $user_id =$row['id'];
				 $user_permission = explode(',',$row['permission']);
				//  if(!in_array($_GET['page'],$user_permission)){
				// 	header('location:'.SITEURL.'admin/denied.php'); 
				//  }
				 //print_r($user_permission);
			 }
		 }
	}
?>