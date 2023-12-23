<?php
include('../languages/lang_config.php');
include('../admin/config/apply.php');
include('../includes/functions.php');


$temp_bookings = get_tempBookingDateTime();  
foreach($temp_bookings as $key=>$booking){
	$id = $booking['id'];
	$temp_booking_date = $booking['temp_booking_date'];
	$temp_booking_time = $booking['temp_booking_time'];
    $is_booking = get_bookingByDateTime($temp_booking_date,$temp_booking_time);
	if(count($is_booking) == 0){
		delete_tempBookingDateTime($id); 
	}

}

?>
