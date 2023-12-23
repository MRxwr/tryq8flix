<?php
include('../languages/lang_config.php');
include('../admin/config/apply.php');
include('../includes/functions.php');
$postData=$_SESSION['postData'];
$InvoiceData=$_SESSION['InvoiceData'];

$cardDetails ='{
  "paymentType": "card",
  "card": {
          "Number":"'.$_POST['cardNumber'].'",
          "expiryMonth":"'.$_POST['exp_month'].'",
          "expiryYear":"'.$_POST['exp_year'].'",
          "securityCode":"'.$_POST['cvv'].'"
  },
  "saveToken": true
}';
####### Initiate Payment ######
$token = "fVysyHHk25iQP4clu6_wb9qjV3kEq_DTc1LBVvIwL9kXo9ncZhB8iuAMqUHsw-vRyxr3_jcq5-bFy8IN-C1YlEVCe5TR2iCju75AeO-aSm1ymhs3NQPSQuh6gweBUlm0nhiACCBZT09XIXi1rX30No0T4eHWPMLo8gDfCwhwkbLlqxBHtS26Yb-9sx2WxHH-2imFsVHKXO0axxCNjTbo4xAHNyScC9GyroSnoz9Jm9iueC16ecWPjs4XrEoVROfk335mS33PJh7ZteJv9OXYvHnsGDL58NXM8lT7fqyGpQ8KKnfDIGx-R_t9Q9285_A4yL0J9lWKj_7x3NAhXvBvmrOclWvKaiI0_scPtISDuZLjLGls7x9WWtnpyQPNJSoN7lmQuouqa2uCrZRlveChQYTJmOr0OP4JNd58dtS8ar_8rSqEPChQtukEZGO3urUfMVughCd9kcwx5CtUg2EpeP878SWIUdXPEYDL1eaRDw-xF5yPUz-G0IaLH5oVCTpfC0HKxW-nGhp3XudBf3Tc7FFq4gOeiHDDfS_I8q2vUEqHI1NviZY_ts7M97tN2rdt1yhxwMSQiXRmSQterwZWiICuQ64PQjj3z40uQF-VHZC38QG0BVtl-bkn0P3IjPTsTsl7WBaaOSilp4Qhe12T0SRnv8abXcRwW3_HyVnuxQly_OsZzZry4ElxuXCSfFP2b4D2-Q";
$basURL = "https://apitest.myfatoorah.com";
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "$basURL/v2/InitiatePayment",
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $InvoiceData,
  CURLOPT_HTTPHEADER => array("Authorization: Bearer $token","Content-Type: application/json"),
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo "$response '<br />'";
}

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
} else {
  echo "$response '<br />'";
  $json  = json_decode((string)$response, true);
  //echo "json  json: $json '<br />'";
 
 $payment_url = $json["Data"]["PaymentURL"];
 
     # after getting the payment url call it as a post API and pass card info to it
     # if you saved the card info before you can pass the token for the api
     
 $curl = curl_init();
 curl_setopt_array($curl, array(
   CURLOPT_URL => "$payment_url",
   CURLOPT_CUSTOMREQUEST => "POST",
   CURLOPT_POSTFIELDS => $cardDetails,
   CURLOPT_HTTPHEADER => array("Authorization: Bearer $token","Content-Type: application/json"),
 ));
 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
 $response = curl_exec($curl);
 $err = curl_error($curl);
 
 curl_close($curl);
 
 if ($err) {
   echo "cURL Error #:" . $err;
 } else {
   echo "$response '<br />'";
   $_SESSION['add'] = "<div class='success'>".$lang['add_success']."</div>";
   exit;
    //header('location:'.SITEURL.'index.php?page=booking-complete');
 }

}
?>
