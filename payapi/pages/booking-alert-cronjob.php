<?php 
$currentDate = date("yy-m-d");
$bookings = get_activeBookingData($currentDate);  
foreach($bookings as $key=>$booking){
	$bookingDate = $booking['booking_date'];
	
	$currentDate = strtotime($currentDate); 
    $bookingDate = strtotime($bookingDate);
	
	$dateDiff = ($booking_date - $currentDate)/60/60/24; 
	if($dateDiff == 10){
		$package=get_packages_details($booking['package_id']);
 
		 
         //sendkwtsms($mobile,$message);
	}
}

?> 