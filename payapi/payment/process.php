<?php
include('../languages/lang_config.php');
include('../admin/config/apply.php');
include('../includes/functions.php');
if(isset($_POST['submit']))
{
    //var_dump($_POST);
    //exit;
	$select_extra_item = $_POST['select_extra_item'];
	$comm="";
	$extra_price = 0;
	$select_extra_item_val ="";
	for($i = 0; $i<count($select_extra_item); $i++){
		$select_extra_item_arr = explode(",",$select_extra_item[$i]);
		$arr = array('item' => $select_extra_item_arr[0],'price' => $select_extra_item_arr[1]); 
		$extra_price = $extra_price + $select_extra_item_arr[1];
		//$select_extra_item_val .= $comm.json_encode($arr);
		$select_extra_item_val .= $comm.json_encode($arr,JSON_UNESCAPED_UNICODE);
		$comm=",";
	}
	
	$extra_items = "[".$select_extra_item_val."]";

     $package_id = $obj->sanitize($conn,$_POST['id']);
			$booking_date = $obj->sanitize($conn,$_POST['booking_date']);
			$date = explode('-',$booking_date);
			$booking_date = $date[2].'-'.$date[1].'-'.$date[0];
			$booking_time = $_POST['booking_time'];
			if(isset($select_extra_item)){ $is_filming = 1; }else{ $is_filming =0; }
			$customer_name = $_POST['customer_name'];
			$customer_email = $_POST['customer_email'];
			$arabic = ['١','٢','٣','٤','٥','٦','٧','٨','٩','٠'];
			$english = [ 1 ,  2 ,  3 ,  4 ,  5 ,  6 ,  7 ,  8 ,  9 , 0];
			$phone = str_replace($arabic, $english, $_POST['mobile_number']);
			$mobile_number = $phone;
			if(isset($_POST['plebel'])){ $package_lebel = $_POST['plebel']; }else{ $package_lebel =''; }
			if(isset($_POST['theme'])){ $package_theme = $_POST['theme']; }else{ $package_theme =''; }
			if(isset($_POST['discount_price'])){ $discount_price = $_POST['discount_price']; }else{ $discount_price =''; }
			if(isset($_POST['coupon_code'])){ $coupon_code = $_POST['coupon_code']; }else{ $coupon_code =''; }
			if(isset($_POST['referral_code'])){ $referral_code = $_POST['referral_code']; }else{ $referral_code =''; }
			if(isset($_POST['baby_name'])){ $baby_name = $_POST['baby_name']; }else{ $baby_name =''; }
			if(isset($_POST['baby_age'])){ $baby_age = $_POST['baby_age']; }else{ $baby_age =''; }
			if(isset($_POST['instructions'])){ $instructions = $_POST['instructions']; }else{ $instructions =''; }
					date_default_timezone_set('Asia/Riyadh');
					$created_at = date('Y-m-d H:i:s');
			
		
				if(isset($_POST['lebel_price'])){ $lebel_price = $_POST['lebel_price']; }else{ $lebel_price =0; }
				if($is_filming == 1){
						$booking_price = $_POST['booking_price'];
						$booking_price = $booking_price + $extra_price + $lebel_price;
				} else {
						$booking_price = $_POST['booking_price']+$lebel_price;
				}
		 if(check_bookingTimeAnddate($booking_date,$booking_time)){
				$retuen_url = SITEURL.'?page=reservations&id='.$package_id;
				 header('location:'.$retuen_url);
			}	 

      $package = get_packages_details($package_id);
      $package_title = $package['title_'.$_SESSION['lang']];
      $customer_mobile=$mobile_number;
    
      $postData='{
        "PaymentMethodId":"1",
        "CustomerName": "'.$customer_name.'",
        "DisplayCurrencyIso": "KWD",
        "MobileCountryCode":"+965",
        "CustomerMobile": "'.$customer_mobile.'",
        "CustomerEmail": "'.$customer_email.'",
        "InvoiceValue": 30.500,
        "CallBackUrl": "'.SITEURL.'index.php?page=booking-complete",
        "ErrorUrl": "'.SITEURL.'index.php?page=booking-faild",
        "Language": "en",
        "CustomerReference" :"Ref 0009",
        "ExpireDate": "",
		"SupplierCode": 9,
        "InvoiceItems": [
          {
            "ItemName": "'.$package_title.' ['.$booking_date.'] ['.$booking_time.']",
            "Quantity": 1,
            "UnitPrice": 30.500,
          }
        ]
      }';
     
####### Initiate Payment ######
//$token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL"; 
//$basURL = "https://apitest.myfatoorah.com";
$token = "hE-2B3TuAQ-ea717-mLkkfajc240Eh4PmRFLRugNAw3aQMTfsNaL9_IsHPKEYQ7P7Ov7AyXRk_JRTOEOP9aNt6KbOx5bWU7P60vqFEHyMSqGXMyTyFzR7knj9eJukd-fr2VKK0Ti0Xic2z7dmYeZAQ8gZd_LOmDHy8kMqBaL6Sgom0HRGJxNXy8dIqcyVe2vgJ5fjE40NzrTKktY9E5_3ELgTi5qFgAZTDk76WmblxT36oCZqAs2BxhBVD2-3uQbrEN3FtdQ8sladuRF5CX4znVQ7eSXZ1UyzcDiW2FqyNVbU2JasG9MC2u8Cz5xLKO1dU8PDXaETqeDJ-8DLxQ-1fed7NqJKSPnGOMwSrSRDIzCqRtqeXVVaqgngy0GDM88NRusZmBq73zqao577UfZcGjNGo-hlbPYS_0gYm-fAla0OkZeZjAJCgrDNTu0L1As0P27crSu2LUl6MNZn5iHkd1lUiCnRPwE7Ppky1C_t-l6lCuQcv-hkV9fv-EbcsIdnhBZhzG7_QG9jEZVjpj_FxcSTlv0EraCdI9O4rd0-pYesfbEEAE6YseARJ4iRXXVOYzy_lMLqGfu1kw_bOjJp1VPCMJA78N6uIh9PFdozgfBq6-UkDTCOEnozsRsILfO96buzhRRF0Czkde4NvBzt7jAPoqbEFcOn4mwzkLa_qDPOoVMOsQc12Vgcsb7klV0ktRJBA";
$basURL = "https://api.myfatoorah.com";

####### Execute Payment ######
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "$basURL/v2/ExecutePayment",
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $postData,
  CURLOPT_HTTPHEADER => array("Authorization: Bearer $token","Content-Type: application/json"),
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
}else {
  //echo "$response '<br />'";
    $json = json_decode($response, true);
    $payment_url = $json["Data"]["PaymentURL"];
    $orderId = $json["Data"]["InvoiceId"];
    $tbl_name = 'tbl_booking';
			$data= "
				package_id = '$package_id',
				transaction_id = '$orderId',
				booking_date = '$booking_date',
				booking_time = '$booking_time',
				is_filming = '$is_filming',
				extra_items = '$extra_items',
				booking_price = '$booking_price',
				customer_name = '$customer_name',
				mobile_number = '$mobile_number',
				baby_name = '$baby_name',
				baby_age = '$baby_age',
				package_lebel = '$package_lebel',
				theme = '$package_theme',
				discount_price = '$discount_price',
				coupon_code = '$coupon_code',
				referral_code = '$referral_code',
				instructions = '$instructions',
				status ='No',
				created_at = '$created_at'
				";
			$query = $obj->insert_data($tbl_name,$data);
			$res = $obj->execute_query($conn,$query);
			if($res==true){
        	$last_id = mysqli_insert_id($conn);
        	header('location:'.$payment_url);
			}else{
				//Failed to Add Categoy
			}

  }
}
?>
