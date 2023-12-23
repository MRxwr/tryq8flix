<?php 
include('../languages/lang_config.php');
include('../admin/config/apply.php');
include('../includes/functions.php');
// session id
$session_id = session_id();
  $time = $_POST['time'];
  $date = $_POST['date'];
  $temp_booking_date = $obj->sanitize($conn,$date);
  $temp_date = explode('-',$temp_booking_date);
  $temp_booking_date = $temp_date[2].'-'.$temp_date[1].'-'.$temp_date[0];
  $searches=get_tempBookingDateTimeNotSession($temp_booking_date,$time,$session_id);
  
		if(@count($searches) == 1){
			echo $searches = 1;
		} else {
			$searchesSession=get_tempBookingDateTimeWithSession($temp_booking_date,$session_id);
			if(@count($searchesSession) == 0){
				///////////////////////// Add tbl_temp_date_time  ////////////////////
					$tbl_name = 'tbl_temp_date_time';
					$data= "
					session_id = '$session_id',
					temp_booking_date = '$temp_booking_date',
					temp_booking_time = '$time'
					";
					$query = $obj->insert_data($tbl_name,$data);
					$res = $obj->execute_query($conn,$query);
		
				 //////////// End ////////////////	
			} else {
				$tbl_name = 'tbl_temp_date_time';
					$data= "
					temp_booking_date = '$temp_booking_date',
					temp_booking_time = '$time'
					";
					$where = "	session_id = '$session_id'";
					$query = $obj->update_data($tbl_name,$data,$where);
					$res = $obj->execute_query($conn,$query);
			}
		}
?>