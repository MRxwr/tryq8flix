<?php
header('Content-type: application/json');
$token = "hE-2B3TuAQ-ea717-mLkkfajc240Eh4PmRFLRugNAw3aQMTfsNaL9_IsHPKEYQ7P7Ov7AyXRk_JRTOEOP9aNt6KbOx5bWU7P60vqFEHyMSqGXMyTyFzR7knj9eJukd-fr2VKK0Ti0Xic2z7dmYeZAQ8gZd_LOmDHy8kMqBaL6Sgom0HRGJxNXy8dIqcyVe2vgJ5fjE40NzrTKktY9E5_3ELgTi5qFgAZTDk76WmblxT36oCZqAs2BxhBVD2-3uQbrEN3FtdQ8sladuRF5CX4znVQ7eSXZ1UyzcDiW2FqyNVbU2JasG9MC2u8Cz5xLKO1dU8PDXaETqeDJ-8DLxQ-1fed7NqJKSPnGOMwSrSRDIzCqRtqeXVVaqgngy0GDM88NRusZmBq73zqao577UfZcGjNGo-hlbPYS_0gYm-fAla0OkZeZjAJCgrDNTu0L1As0P27crSu2LUl6MNZn5iHkd1lUiCnRPwE7Ppky1C_t-l6lCuQcv-hkV9fv-EbcsIdnhBZhzG7_QG9jEZVjpj_FxcSTlv0EraCdI9O4rd0-pYesfbEEAE6YseARJ4iRXXVOYzy_lMLqGfu1kw_bOjJp1VPCMJA78N6uIh9PFdozgfBq6-UkDTCOEnozsRsILfO96buzhRRF0Czkde4NvBzt7jAPoqbEFcOn4mwzkLa_qDPOoVMOsQc12Vgcsb7klV0ktRJBA"; 

//$token = "uJ8dWiJ1JZOtl_2njKZu-0IYilOulBJmI1HMM2ufaV_bYZ2qk_kehfHxntKfRMcyH8wlA5iYtWhX_ciDNDZGTMqL4CnsF4OiffAIAGE1MffdJLMVqg1qtshsH61G0kBTkiYhlCbVaBpVn57wGXmPcqEpL6MDGQVVh6b97TzKVaRefUs8D8ndhJ59jZLluRc3AJk5OWI0tuhQRy9NWESxXCG8b4p7ju3ecf89mZCnWRuizHtmKCtdLut2cNYrWTX1AnHOrEnVcmQBgdxe9npak-vW_Exnxk3NLMgNoYJJTEcwlqnZmpq1CGk3modK3DkBx_ACRl6pthCTx_yPUMKmYTeoLrwJlBLLEklQEeEhF66Wj9zpvjGjb-6k456HPSFTPsg6KIjX3Uv0UQV4tdLRQsFZUPhEgwvIEJNSuWdE2WQ2XqYfrRqoE48fpxgq7MGIxVQdwecP-bqidV7O1rI8FIucKCMW137RhD8KJZcpioQodYB2TB0iR_qJQ0BWJoIvaJqMYWGZKFrqBRf2c_DYqgm83UuclcRl3f_S0BotTRAjR2KLZMJ3J5T0j2g-ItGHhmMlXz4o4quiAv5VdHoqj9zL9iQr2JLGEeSELWvZFC9mE5rx-VYS3Tk3_7WpsWS7rSZ-tmgxJcfX7lVlDcfj_bIgMIiqiKFSolVaf7n4fXBteS5-HqHvx44tQyKd-l9ZR7n1HBvUaqL2KWSRKUsFBIks8huAS7twsPpc7MySddZX3zHL"; 
#token value to be placed here;
$basURL = "https://api.myfatoorah.com";
$allItems = array();

$allItems[] = array(
					"ItemName"=>"VISA/MASTER TAX",
					"Quantity"=>1,
					"UnitPrice"=>(float)round(13, 2)
					);

$postMethodLines = array(
"PaymentMethodId" => "1",
"CustomerName"=>  "naser",
"DisplayCurrencyIso"=> "KWD", 
"MobileCountryCode"=>"+965",
"CustomerMobile"=> "90949089",
"CustomerEmail"=> "nasserhatab@gmail.com",
"InvoiceValue"=> (float)round(13, 2),
"CallBackUrl"=> "https://soapaljmelakw.com/details",
"ErrorUrl"=> "https://soapaljmelakw.com/checkout.php?status=fail",
"Language"=> "en",
"ExpireDate"=> "",
);
$postMethodLines["InvoiceItems"][0] = $allItems[0];

//print_r($postMethodLines);die();

####### Execute Payment ######
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "$basURL/v2/ExecutePayment",
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode($postMethodLines),
  CURLOPT_HTTPHEADER => array("Authorization: Bearer $token","Content-Type: application/json"),
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);
if ($err) {
  echo "cURL Error #:" . $err;
} else {
  $resultMY = json_decode($response, true);
  
  /*print_r($allItems);
  print_r($postMethodLines);*/
  print_r($resultMY);
  die();
  
  $orderId = $resultMY["Data"]["InvoiceId"];
  $date = date("Y-m-d h:i:s");
  $i = 0;
    while ( $i < sizeof ($_SESSION["cart"]["id"]) )
    {
        $id = $_SESSION["cart"]["id"][$i];
        $quantity = $_SESSION["cart"]["qorder"][$i];
		$size = $_SESSION["cart"]["size"][$i];
        $sql = "INSERT INTO `orders`
                (`date`,`userId`, `orderId`, `email`, `fullName`, `mobile`, `productId`, `quantity`, `discount`, `totalPrice`, `voucher`, `place`, `area`, `block`, `street`, `avenue`, `house`, `notes`, `areaA`, `blockA`, `streetA`, `avenueA`, `building`, `floor`, `apartment`, `notesA`, `country`, `status`, `pMethod`, `d_s_charges`, `size`, `userDiscount`, `creditTax`) 
                VALUES
                ('".$date."', '".$userId."', '".$orderId."', '".$email."', '".$name."', '".$phone."', '".$id."', '".$quantity."', '".$discount."', '".round($totalPrice,2)."', '".$orderVoucher."', '".$place."', '".$area."', '".$block."', '".$street."', '".$avenue."', '".$house."', '".$notes."', '".$areaA."', '".$blockA."', '".$streetA."', '".$avenueA."', '".$building."', '".$floor."', '".$apartment."', '".$notesA."', '".$country."', '0', '".$paymentMethod."', '".$delivery."', '".$size."', '".$userDiscount."', '".round($VisaCard,2)."')
				";
        $result = $dbconnect->query($sql);
        $i++;
    }
  if ( $_POST["paymentMethod"] == 3 )
  {
    $_SESSION["createKW"]["pMethod"] = $_POST["paymentMethod"];
    $_SESSION["createKW"]["orderId"] = $resultMY["Data"]["InvoiceId"];
    header("LOCATION: details.php") ;
  }
  elseif ( $_POST["paymentMethod"] == 1 )
  {
    $_SESSION["createKW"]["pMethod"] = $_POST["paymentMethod"];
    $_SESSION["createKW"]["orderId"] = $resultMY["Data"]["InvoiceId"];
    //header("LOCATION: details.php") ;
    header("LOCATION: " . $resultMY["Data"]["PaymentURL"]) ;
  }
  elseif ( $_POST["paymentMethod"] == 2 )
  {
    $_SESSION["createKW"]["pMethod"] = $_POST["paymentMethod"];
    $_SESSION["createKW"]["orderId"] = $resultMY["Data"]["InvoiceId"];
    //header("LOCATION: details.php") ;
    header("LOCATION: " . $resultMY["Data"]["PaymentURL"]) ;
  }
}
?>