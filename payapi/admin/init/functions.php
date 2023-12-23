<?php 
  function get_setting($set){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_settings';
	$where = "is_active='Yes' and id=1 ";
	$query = $obj->select_data($tbl_name,$where);
	$res = $obj->execute_query($conn,$query);
	if($res == true)
	{
		$count_rows = $obj->num_rows($res);
		if($count_rows>0){
			$data = mysqli_fetch_array($res);
			if(isset($data[$set])){
				return $data[$set];
			}else{
				return false;
			}
				
		}
		 
	}
  }
  // get all active banner
  function get_banners(){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_banners';
	$where = "is_active='Yes'";
	$query = $obj->select_data($tbl_name,$where);
	$res = $obj->execute_query($conn,$query);
	if($res == true)
	{
		$count_rows = $obj->num_rows($res);
		if($count_rows>0){
				return $res;
		}  
	}
	}

	 // get all active categories
	 function get_categories(){
		GLOBAL $obj,$conn;
		$tbl_name = 'tbl_categories';
		$where = "is_active='Yes'";
		$query = $obj->select_data($tbl_name,$where);
		$res = $obj->execute_query($conn,$query);
		if($res == true)
		{
			$count_rows = $obj->num_rows($res);
			if($count_rows>0){
					return $res;
			}  
		}
	}
	
	  // get  package details by package id
  function get_category_details($id){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_categories';
	$where = "is_active='Yes' && id=".$id;
	$query = $obj->select_data($tbl_name,$where);
	$res = $obj->execute_query($conn,$query);
	if($res == true)
	{
	  $count_rows = $obj->num_rows($res);
	  if($count_rows>0){
		//return $res -> fetch_row();
		return mysqli_fetch_array($res);
			  
	  }
	   
  }
}

  // get all active packages	
  function get_packages($cat=0){
	    GLOBAL $obj,$conn;
		$tbl_name = 'tbl_packages';
		if($cat>0){
			$where = "is_active='Yes' and category='$cat'";
		}else{
			$where = "is_active='Yes'";
		}
		
		$query = $obj->select_data($tbl_name,$where);
		$res = $obj->execute_query($conn,$query);
		if($res == true)
		{
			$count_rows = $obj->num_rows($res);
			if($count_rows>0){
					return $res;
			}
			
		}
  }
  
  // get  package details by package id
  function get_packages_details($id){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_packages';
	$where = "is_active='Yes' && id=".$id;
	$query = $obj->select_data($tbl_name,$where);
	$res = $obj->execute_query($conn,$query);
	if($res == true)
	{
	  $count_rows = $obj->num_rows($res);
	  if($count_rows>0){
		//return $res -> fetch_row();
		return mysqli_fetch_array($res);
			  
	  }
	   
  }
}
// get  all galleries image by category id
function get_galleries(){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_galleries';
	$where = "is_active='Yes'";
	$query = $obj->select_data($tbl_name,$where);
	$res = $obj->execute_query($conn,$query);
	if($res == true)
	{
		$count_rows = $obj->num_rows($res);
		if($count_rows>0){
				return $res;
		}
		
	}
}
// get cms page details by page id
function get_page_details($id){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_pages';
	$where = "is_active='Yes' && id=".$id;
	$query = $obj->select_data($tbl_name,$where);
	$res = $obj->execute_query($conn,$query);
	if($res == true)
	{
	  $count_rows = $obj->num_rows($res);
	  if($count_rows>0){
		//return $res -> fetch_row();
		return mysqli_fetch_array($res);	  
	  }   
  }
}

 // get all active packages	
  function get_bookingSearch($searchquery){
	    GLOBAL $obj,$conn;
		$tbl_name = 'tbl_booking';
		$where = "transaction_id =".$searchquery." OR mobile_number = ".$searchquery;
		$query = $obj->select_data($tbl_name,$where);
		$res = $obj->execute_query($conn,$query);
		if($res == true)
		{
			$count_rows = $obj->num_rows($res);
			if($count_rows>0){
					return $res;
			} 
			
		}
  }
  	// get  package details by package id
	  function get_booking_details($bookid){
		GLOBAL $obj,$conn;
		$tbl_name = 'tbl_booking';
		$where = "transaction_id=".$bookid;
		$query = $obj->select_data($tbl_name,$where);
		$res = $obj->execute_query($conn,$query);
		if($res == true)
		{
		  $count_rows = $obj->num_rows($res);
		  if($count_rows>0){
			//return $res -> fetch_row();
			return mysqli_fetch_array($res);		  
		  }
		   
	  }
	}
  
  //send booking sms
	function sendkwtsms($mobile,$message){
		$message = str_replace(' ','+',$message);
		$url = 'http://www.kwtsms.com/API/send/?username=badertov&password=471990Bader&sender=HBQ+Studio&mobile=965'.$mobile.'&lang=1&message='.$message;
		       $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => $url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_CUSTOMREQUEST => "GET",
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
				curl_close($curl);
				if ($err){
					return $err;
				}else{
					  return $response;	
				}
	}
	
	//send booking email
	function sendkwtemail($email,$message){
		$message = $message;
		$email = $email;
        $subject = 'New Booking';
        $EmailTo = "HBQ.photo@gmail.com";
        // prepare email body text
        $Body .= $subject;
        $Body .= "\n";
        $Body .= $message;
        $Body .= "\n";
         
        // send email
        $success = mail($EmailTo, $subject, $Body, "From:".$email);
         
        // redirect to success page
        if ($success){
           echo "success";
        }else{
            echo "invalid";
        }
		
	}

	// get book time by select date
	// get book time by select date
	function get_bookingTimeBydate($id,$date){
	    GLOBAL $obj,$conn;
		$tbl_name = 'tbl_booking';
		$where = " booking_date like '%".$date."%' AND status='Yes'";
		$query = $obj->select_data($tbl_name,$where);
		$res = $obj->execute_query($conn,$query);
		if($res == true)
		{
			$count_rows = $obj->num_rows($res);
			if($count_rows>0){
					return $res;
			} 
			
		}
	}

		// get disabled date
		function get_disabledDate(){
			GLOBAL $obj,$conn;
			$tbl_name = 'tbl_disabled_date';
				$query = $obj->select_data($tbl_name);
			$res = $obj->execute_query($conn,$query);
			if($res == true)
			{
				$count_rows = $obj->num_rows($res);
				if($count_rows>0){
						return $res;
				}
				
			}
	  }

     function check_bookingTimeAnddate($date,$time){
	    GLOBAL $obj,$conn;
		$tbl_name = 'tbl_booking';
		$where = " booking_date like '%".$date."%' AND booking_time like '%".$time."%' AND status='Yes'";
		$query = $obj->select_data($tbl_name,$where);
		$res = $obj->execute_query($conn,$query);
		if($res == true)
		{       
			$count_rows = $obj->num_rows($res);
			if($count_rows>0){
					return true;
			} 
			
		}
	}

  // get temp booking date time	
  function get_tempBookingDateTimeNotSession($date,$time,$session_id){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_temp_date_time';
	$where = " session_id != '".$session_id."' AND temp_booking_date ='".$date."' AND temp_booking_time = '".$time."'";
	$query = $obj->select_data($tbl_name,$where);
	$res = $obj->execute_query($conn,$query);
	if($res == true)
	{
		$count_rows = $obj->num_rows($res);
		if($count_rows>0){
				return $res;
		} 
		
	}
}
	   // get temp booking date time	
  function get_tempBookingDateTimeWithSession($date,$session_id){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_temp_date_time';
	$where = " session_id = '".$session_id."' AND temp_booking_date ='".$date."' ";
	$query = $obj->select_data($tbl_name,$where);
	$res = $obj->execute_query($conn,$query);
	if($res == true)
	{
		$count_rows = $obj->num_rows($res);
		if($count_rows>0){
				return $res;
		} 
		
	}
}

	   // get temp booking data	
  function get_tempBookingDateTime(){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_temp_date_time';
	$query = $obj->select_data($tbl_name,$where);
	$res = $obj->execute_query($conn,$query);
	if($res == true)
	{
		$count_rows = $obj->num_rows($res);
		if($count_rows>0){
				return $res;
		} 
		
	}
}

	   // get  booking date time	
  function get_bookingByDateTime($temp_booking_date,$temp_booking_time){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_booking';
	$where = " booking_date ='".$temp_booking_date."' AND booking_time = '".$temp_booking_time."' AND status = 'Yes'";
	$query = $obj->select_data($tbl_name,$where);
	$res = $obj->execute_query($conn,$query);
	if($res == true)
	{
		$count_rows = $obj->num_rows($res);
		if($count_rows>0){
				return $res;
		} 
		
	}
}
// delete temp booking data	
  function delete_tempBookingDateTime($id){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_temp_date_time';
	$where = " id ='".$id."'";
	$query = $obj->delete_data($tbl_name, $where);
	$res = $obj->execute_query($conn,$query);

}
// delete temp booking data	
  function delete_tempBookingDateTimeBySession($session_id){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_temp_date_time';
	$where = " session_id ='".$session_id."'";
	$query = $obj->delete_data($tbl_name, $where);
	$res = $obj->execute_query($conn,$query);

}

// Function to get all the dates in given range 
function getDatesFromRange($start, $end, $format = 'd-m-Y') { 
      
  // Declare an empty array 
  $array = array(); 
    
  // Variable that store the date interval 
  // of period 1 day 
  $interval = new DateInterval('P1D'); 

  $realEnd = new DateTime($end); 
  $realEnd->add($interval); 

  $period = new DatePeriod(new DateTime($start), $interval, $realEnd); 

  // Use loop to store date into array 
  foreach($period as $date) {                  
      $array[] = $date->format($format);  
  } 

  // Return the array elements 
  return $array; 
} 

?>

