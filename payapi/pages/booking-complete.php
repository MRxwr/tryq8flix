<?php
date_default_timezone_set('Asia/Riyadh');
$check = ["'",'"',")","(",";","?",">","<","~","!","#","$","%","^","&","*","-","_","=","+","/","|",":"];
if ( !isset($_GET["paymentId"]) )
{
    header("LOCATION: booking-faild.php");
    die();
}
else{
//$token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL"; 
	
 $token = "hE-2B3TuAQ-ea717-mLkkfajc240Eh4PmRFLRugNAw3aQMTfsNaL9_IsHPKEYQ7P7Ov7AyXRk_JRTOEOP9aNt6KbOx5bWU7P60vqFEHyMSqGXMyTyFzR7knj9eJukd-fr2VKK0Ti0Xic2z7dmYeZAQ8gZd_LOmDHy8kMqBaL6Sgom0HRGJxNXy8dIqcyVe2vgJ5fjE40NzrTKktY9E5_3ELgTi5qFgAZTDk76WmblxT36oCZqAs2BxhBVD2-3uQbrEN3FtdQ8sladuRF5CX4znVQ7eSXZ1UyzcDiW2FqyNVbU2JasG9MC2u8Cz5xLKO1dU8PDXaETqeDJ-8DLxQ-1fed7NqJKSPnGOMwSrSRDIzCqRtqeXVVaqgngy0GDM88NRusZmBq73zqao577UfZcGjNGo-hlbPYS_0gYm-fAla0OkZeZjAJCgrDNTu0L1As0P27crSu2LUl6MNZn5iHkd1lUiCnRPwE7Ppky1C_t-l6lCuQcv-hkV9fv-EbcsIdnhBZhzG7_QG9jEZVjpj_FxcSTlv0EraCdI9O4rd0-pYesfbEEAE6YseARJ4iRXXVOYzy_lMLqGfu1kw_bOjJp1VPCMJA78N6uIh9PFdozgfBq6-UkDTCOEnozsRsILfO96buzhRRF0Czkde4NvBzt7jAPoqbEFcOn4mwzkLa_qDPOoVMOsQc12Vgcsb7klV0ktRJBA"; 
//token value to be placed here;
	$basURL = "https://api.myfatoorah.com";

	$invoiceArray = 
	[
		"Key" => $_GET["paymentId"],
		"KeyType" => 'paymentId'
	];
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "$basURL/v2/GetPaymentStatus",
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => json_encode($invoiceArray),
	  CURLOPT_HTTPHEADER => array("Authorization: Bearer $token","Content-Type: application/json"),
	));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);
	if ($err) 
	{
		echo "cURL Error #:" . $err;
	} 
	else 
	{
		$resultMY = json_decode($response, true);
    $orderId = $resultMY["Data"]["InvoiceId"];
    $booking = get_booking_details($orderId);

      $id=$booking['id'];

      $query = "UPDATE tbl_booking SET status='Yes' WHERE id=$id";
      $res = $obj->execute_query($conn,$query);

			if($res==true){
         $package=get_packages_details($booking['package_id']);
         $id = $package['id'];
         $price = $package['price'];
         $currency = $package['currency'];
         $post_title = $package['title_'.$_SESSION['lang']];
         $post_description = $package['description_'.$_SESSION['lang']];
         $image_url =$package['image_url'];
         $created_at = $package['created_at'];
         $is_extra = $package['is_extra']; 
         $extra_items = $package['extra_items'];
         $booking_date = $booking['booking_date'];
         $booking_time  = $booking['booking_time'];
         $mobile = $booking['mobile_number'];
         $customer_name = $booking['customer_name'];
         $arabic = ['١','٢','٣','٤','٥','٦','٧','٨','٩','٠'];
	     $english = [ 1 ,  2 ,  3 ,  4 ,  5 ,  6 ,  7 ,  8 ,  9 , 0];
	     $phone = str_replace($arabic, $english, $booking['mobile_number']);
	     $mobile = $phone;
       $message="Your booking has been confirmed for Haya-Photography, Date: ".$booking_date.", Time:".$booking_time.",Id: ".$orderId;
         sendkwtsms($mobile,$message);
         @sendkwtemail('info@hbqphoto.com',$message);
         ///////////////// Check booking slot //////////////////////////////
          $booktimes = get_bookingTimeBydate('', $booking_date);
          $booktimeArr=array(); 
          if(@count($booktimes) != 0){
            foreach($booktimes as $key=>$booktime){		
                $booktimeArr[] = $booktime['booking_time'];
             }
            }
            $times = $package['time'];
            $rows = json_decode($times); 
                          $timeSlotAvailable = 0;
                              foreach($rows as $row ){
								  $time = $row->startDate." - ".$row->endDate;
                                  if (!in_array($time, $booktimeArr))
                                  {
                    $timeSlotAvailable = 1;
                                  } 
                  }

            ///////////////////////// Update tbl_disabled_date table /////////////////////
            if($timeSlotAvailable == 0){
              $date = explode('-',$booking_date);
                $booking_date_format = $date[1].'/'.$date[2].'/'.$date[0];

            $data="
              disabled_date='$booking_date_format'
            ";
            $tbl_name='tbl_disabled_date';
            $query = $obj->insert_data($tbl_name,$data);
            $res = $obj->execute_query($conn,$query);
            }
        ?>
   <section>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h2 class="shoots-Head2"><?php echo $lang['reservation_complete'] ?>
            <span class="theme-bg ml-2" style="border-radius: 30px; color:#FFF; padding: 4px 7px; font-size: 24px;">
              <i class="fa fa-check"></i>
            </span>
          </h2>
        </div>
        <div class="col-md-10 col-sm-10">
          <div class="personal-information">
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['reservation_number'] ?></label>
              <div class="col-sm-7 col-md-8">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?=$orderId?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['package_choosen'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?=$post_title?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['date'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?=$booking_date;?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['preffered_time'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?=$booking_time;?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['customer_name'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?=$booking['customer_name'];?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['mobile_number'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?=$booking['mobile_number'];?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['baby_name'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?=$booking['baby_name'];?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['baby_age'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?=$booking['baby_age'];?> Years">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['instructions'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?=$booking['instructions'];?>">
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="" class="col-12 col-form-label"><?php echo $lang['NoteText'] ?>:</label>
            <div class="col-12">
              <ul class="list-unstyled h5">
                <li>- <?php echo $lang['ReceiveSMS'] ?>.</li>
                <li>- <?php echo $lang['SMSThreeDays'] ?>.</li>
                
              </ul>
            </div>
          </div>
          <div class="row pt-4">
            <div class="col-sm-7 col-md-6">
              <a href="<?php echo SITEURL; ?>" class="btn btn-lg btn-outline-primary btn-block btn-rounded"><?php echo $lang['goto_home'] ?></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>   
<?php	}
  }
}
?>