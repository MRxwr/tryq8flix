<?php 
		if(isset($_POST['submit']))
		{
			//echo "Click";
			$package_id = $obj->sanitize($conn,$_POST['id']);
			$booking_date = $obj->sanitize($conn,$_POST['booking_date']);
			$date = explode('-',$booking_date);
			$booking_date = $date[2].'-'.$date[1].'-'.$date[0];
			$booking_time = $_POST['booking_time'];
			if(isset($_POST['is_filming'])){ $is_filming = $_POST['is_filming']; }else{ $is_filming =0; }
			$customer_name = $_POST['customer_name'];
			$customer_email = $_POST['customer_email'];
			$mobile_number = $_POST['mobile_number'];
			if(isset($_POST['baby_name'])){ $baby_name = $_POST['baby_name']; }else{ $baby_name =''; }
			if(isset($_POST['baby_age'])){ $baby_age = $_POST['baby_age']; }else{ $baby_age =''; }
			if(isset($_POST['instructions'])){ $instructions = $_POST['instructions']; }else{ $instructions =''; }
					$created_at = date('Y-m-d H:i:s');
		
			if($is_filming == 1){
				$booking_price = $_POST['booking_price'];
				$hid_extra_items = $_POST['hid_extra_items'];
				 $rows = json_decode($hid_extra_items); 
                        foreach($rows as $row ){
                           $booking_price = $booking_price + $row->price;
                        }
			} else {
				$booking_price = $_POST['booking_price'];
            }
           $package = get_packages_details($package_id);
           $post_title = $package['title_'.$_SESSION['lang']];
      
			$tbl_name = 'tbl_booking';
			$data= "
				package_id = '$package_id',
				booking_date = '$booking_date',
				booking_time = '$booking_time',
				is_filming = '$is_filming',
				booking_price = '$booking_price',
				customer_name = '$customer_name',
				mobile_number = '$mobile_number',
				baby_name = '$baby_name',
				baby_age = '$baby_age',
        		instructions = '$instructions',
        		status =2,
				created_at = '$created_at'
				";
			$query = $obj->insert_data($tbl_name,$data);
			$res = $obj->execute_query($conn,$query);
			if($res==true){
        $last_id = mysqli_insert_id($conn);
        $_SESSION['post'] = $_POST;
        $_SESSION['post_title'] = $post_title;
        $_SESSION['InvoiceValue'] = $booking_price;
        $_SESSION['booking_id'] = $last_id;
				//Category Successfully Added
				//$_SESSION['add'] = "<div class='success'>".$lang['add_success']."</div>";
        //header('location:'.SITEURL.'index.php?page=booking-complete');
        include('pay-now.php');
			}else{
				//Failed to Add Categoy
			}
		}
	?>