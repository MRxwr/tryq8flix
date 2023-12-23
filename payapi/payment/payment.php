<?php
session_start();
header('Content-type: application/json');
require("admin/includes/config.php");
require("includes/checksouthead.php");

ini_set( 'precision', 4 );
ini_set( 'serialize_precision', -1 );

$check = ["'",'"',")","(",";","?",">","<","~","!","#","$","%","^","&","*","-","_","=","+","/","|",":"];

$totalPrice = $_POST["totalPrice"];
$_SESSION["createKW"]["totalPrice"] = $totalPrice;
$paymentMethod = $_POST["paymentMethod"];

if ( isset($userID) AND !empty($userID) )
{
  $userId = $userID;
}
else
{
  $userId = 0;
}

date_default_timezone_set('Asia/Riyadh');
$place = str_replace($check, "", $_SESSION["createKW"]["place"]);
$name = str_replace($check, "", $_SESSION["createKW"]["name"]);
$phone = str_replace($check, "", $_SESSION["createKW"]["phone"]);
$email = str_replace($check, "", $_SESSION["createKW"]["email"]);
$delivery = str_replace($check, "", $_SESSION["createKW"]["delivery"]);
$country = str_replace($check, "", $_SESSION["createKW"]["country"]);
if ( isset($_POST["paymentMethod"]))
{
    $paymentMethod = $_POST["paymentMethod"];
}
else
{
    $paymentMethod = str_replace($check, "", $_SESSION["createKW"]["pMethod"]);
}

if ( $place == 1 )
{
    $area = str_replace($check, "", $_SESSION["createKW"]["area"]);
    $block = str_replace($check, "", $_SESSION["createKW"]["block"]);
    $street = str_replace($check, "", $_SESSION["createKW"]["street"]);
    $house = str_replace($check, "", $_SESSION["createKW"]["house"]);
    $avenue = str_replace($check, "", $_SESSION["createKW"]["avenue"]);
    $notes = str_replace($check, "", $_SESSION["createKW"]["notes"]);
    $areaA = "";
    $building = "";
    $floor = "";
    $apartment = "";
    $blockA = "";
    $streetA = "";
    $avenueA = "";
    $notesA = "";
}
elseif( $place == 3 )
{
	$area = str_replace($check, "", $_SESSION["createKW"]["area"]);
    $block = str_replace($check, "", $_SESSION["createKW"]["block"]);
    $street = str_replace($check, "", $_SESSION["createKW"]["street"]);
    $house = str_replace($check, "", $_SESSION["createKW"]["house"]);
    $avenue = str_replace($check, "", $_SESSION["createKW"]["avenue"]);
    $notes = str_replace($check, "", $_SESSION["createKW"]["notes"]);
    $areaA = "";
    $building = "";
    $floor = "";
    $apartment = "";
    $blockA = "";
    $streetA = "";
    $avenueA = "";
    $notesA = "";
}
else
{
    $building = str_replace($check, "", $_SESSION["createKW"]["building"]);
    $floor = str_replace($check, "", $_SESSION["createKW"]["floor"]);
    $apartment = str_replace($check, "", $_SESSION["createKW"]["apartment"]);
    $areaA = str_replace($check, "", $_SESSION["createKW"]["areaA"]);
    $blockA = str_replace($check, "", $_SESSION["createKW"]["blockA"]);
    $streetA = str_replace($check, "", $_SESSION["createKW"]["streetA"]);
    $avenueA = str_replace($check, "", $_SESSION["createKW"]["avenueA"]);
    $notesA = str_replace($check, "", $_SESSION["createKW"]["notesA"]);
    $area = "";
    $block = "";
    $street= "";
    $house = "";
    $avenue = "";
    $notes = "";
}

if ( isset($_SESSION["createKW"]["orderVoucher"]) AND !empty($_SESSION["createKW"]["orderVoucher"]) )
{
    $orderVoucher = $_SESSION["createKW"]["orderVoucher"];
}
else
{
    $orderVoucher = "";
}
$sql = "SELECT `percentage`,`voucher`
        FROM `vouchers`
        WHERE `id` = '".$orderVoucher."'
        ";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$discount = $row["percentage"];
$_SESSION["createKW"]["discount"] = $discount;
$_SESSION["createKW"]["voucher"] = $row["voucher"];

//$token = "hE-2B3TuAQ-ea717-mLkkfajc240Eh4PmRFLRugNAw3aQMTfsNaL9_IsHPKEYQ7P7Ov7AyXRk_JRTOEOP9aNt6KbOx5bWU7P60vqFEHyMSqGXMyTyFzR7knj9eJukd-fr2VKK0Ti0Xic2z7dmYeZAQ8gZd_LOmDHy8kMqBaL6Sgom0HRGJxNXy8dIqcyVe2vgJ5fjE40NzrTKktY9E5_3ELgTi5qFgAZTDk76WmblxT36oCZqAs2BxhBVD2-3uQbrEN3FtdQ8sladuRF5CX4znVQ7eSXZ1UyzcDiW2FqyNVbU2JasG9MC2u8Cz5xLKO1dU8PDXaETqeDJ-8DLxQ-1fed7NqJKSPnGOMwSrSRDIzCqRtqeXVVaqgngy0GDM88NRusZmBq73zqao577UfZcGjNGo-hlbPYS_0gYm-fAla0OkZeZjAJCgrDNTu0L1As0P27crSu2LUl6MNZn5iHkd1lUiCnRPwE7Ppky1C_t-l6lCuQcv-hkV9fv-EbcsIdnhBZhzG7_QG9jEZVjpj_FxcSTlv0EraCdI9O4rd0-pYesfbEEAE6YseARJ4iRXXVOYzy_lMLqGfu1kw_bOjJp1VPCMJA78N6uIh9PFdozgfBq6-UkDTCOEnozsRsILfO96buzhRRF0Czkde4NvBzt7jAPoqbEFcOn4mwzkLa_qDPOoVMOsQc12Vgcsb7klV0ktRJBA"; 

$token = "cxu2LdP0p0j5BGna0velN9DmzKJTrx3Ftc0ptV8FmvOgoDqvXivkxZ_oqbi_XM9k7jgl3SUriQyRE2uaLWdRumxDLKTn1iNglbQLrZyOkmkD6cjtpAsk1_ctrea_MeOQCMavsQEJ4EZHnP4HoRDOTVRGvYQueYZZvVjsaOLOubLkdovx6STu9imI1zf5OvuC9rB8p0PNIR90rQ0-ILLYbaDZBoQANGND10HdF7zM4qnYFF1wfZ_HgQipC5A7jdrzOoIoFBTCyMz4ZuPPPyXtb30IfNp47LucQKUfF1ySU7Wy_df0O73LVnyV8mpkzzonCJHSYPaum9HzbvY5pvCZxPYw39WGo8pOMPUgEugtaqepILwtGKbIJR3_T5Iimm_oyOoOJFOtTukb_-jGMTLMZWB3vpRI3C08itm7ealISVZb7M3OMPPXgcss9_gFvwYND0Q3zJRPmDASg5NxRlEDHWRnlwNKqcd6nW4JJddffaX8p-ezWB8qAlimoKTTBJCe5CnjT4vNjnWlJWscvk38VNIIslv4gYpC09OLWn4rDNeoUaGXi5kONdEQ0vQcRjENOPAavP7HXtW1-Vz83jMlU3lDOoZsdEKZReNYpvdFrGJ5c3aJB18eLiPX6mI4zxjHCZH25ixDCHzo-nmgs_VTrOL7Zz6K7w6fuu_eBK9P0BDr2fpS"; 
#token value to be placed here;
$basURL = "https://apitest.myfatoorah.com";
$allItems = array();

for ( $i = 0; $i < sizeof($_SESSION["cart"]["id"]); $i++ )
{
	$sql = "SELECT `enTitle`, `price`, `discount`
			FROM 
			`products`
			WHERE 
			`id` LIKE '".$_SESSION["cart"]["id"][$i]."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	if ( isset($_SESSION["createKW"]["orderVoucher"]) )
	{
		$unitPrice = $row["price"] - ($row["price"] * $row["discount"]/100);
		$unitPrice = $unitPrice - ($unitPrice * $discount/100);
	}
	else
	{
		$unitPrice = $row["price"] - ($row["price"] * $row["discount"]/100);
	}
	$allItems[] = array(
					"ItemName"=>$row["enTitle"],
					"Quantity"=>$_SESSION["cart"]["qorder"][$i],
					"UnitPrice"=>(float)$unitPrice
					);
  }
$allItems[] = array(
					"ItemName"=>"Shipping-Delivery",
					"Quantity"=>1,
					"UnitPrice"=>(float)$delivery
					);

//print_r($allItems);die();


$postMethodLines = array(
"PaymentMethodId" => "$paymentMethod",
"CustomerName"=>  "$name",
"DisplayCurrencyIso"=> "KWD", 
"MobileCountryCode"=>"+965",
"CustomerMobile"=> "$phone",
"CustomerEmail"=> "$email",
"InvoiceValue"=> (float)$totalPrice,
"CallBackUrl"=> "https://www.modabymum.com/details",
"ErrorUrl"=> "https://www.modabymum.com/checkout.php?status=fail",
"Language"=> "en",
"CustomerReference" =>"ref 0001",
"ExpireDate"=> "",
"SupplierCode"=>1,
);
for ( $i = 0; $i < (sizeof($_SESSION["cart"]["id"])+1) ; $i++  )
{
	$postMethodLines["InvoiceItems"][$i] = $allItems[$i];
}

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
  //echo print_r($resultMY);die();
  $orderId = $resultMY["Data"]["InvoiceId"];
  $date = date("Y-m-d h:i:s");
  $i = 0;
    while ( $i < sizeof ($_SESSION["cart"]["id"]) )
    {
        $id = $_SESSION["cart"]["id"][$i];
        $quantity = $_SESSION["cart"]["qorder"][$i];
		$color = $_SESSION["cart"]["color"][$i];
		$size = $_SESSION["cart"]["size"][$i];
		$length = $_SESSION["cart"]["length"][$i];
        $sql = "INSERT INTO `orders`
                (`date`,`userId`, `orderId`, `email`, `fullName`, `mobile`, `productId`, `quantity`, `discount`, `totalPrice`, `voucher`, `place`, `area`, `block`, `street`, `avenue`, `house`, `notes`, `areaA`, `blockA`, `streetA`, `avenueA`, `building`, `floor`, `apartment`, `notesA`, `country`, `status`, `pMethod`, `d_s_charges`, `color`, `size`, `length`) 
                VALUES
                ('".$date."', '".$userId."', '".$orderId."', '".$email."', '".$name."', '".$phone."', '".$id."', '".$quantity."', '".$discount."', '".$totalPrice."', '".$orderVoucher."', '".$place."', '".$area."', '".$block."', '".$street."', '".$avenue."', '".$house."', '".$notes."', '".$areaA."', '".$blockA."', '".$streetA."', '".$avenueA."', '".$building."', '".$floor."', '".$apartment."', '".$notesA."', '".$country."', '0', '".$paymentMethod."', '".$delivery."', '".$color."', '".$size."', '".$length."')
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