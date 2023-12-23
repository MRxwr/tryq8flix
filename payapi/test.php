<?php 

$postData = '{
	["endpoint"]=>
	string(29) "PaymentRequestExicuteForStore"
	["apikey"]=>
	string(19) "CKW-1616996182-7527"
	["PaymentMethodId"]=>
	string(1) "1"
	["CustomerName"]=>
	string(11) "NASER HATAB"
	["DisplayCurrencyIso"]=>
	string(3) "KWD"
	["MobileCountryCode"]=>
	string(4) "+965"
	["CustomerMobile"]=>
	string(8) "90949089"
	["CustomerEmail"]=>
	string(21) "nasserhatab@gmail.com"
	["CustomerAddress"]=>
	array(5) {
	  ["Block"]=>
	  string(1) "4"
	  ["Street"]=>
	  string(2) "10"
	  ["HouseBuildingNo"]=>
	  string(1) "4"
	  ["Address"]=>
	  string(4) "adan"
	  ["AddressInstructions"]=>
	  string(0) ""
	}
	["ShippingConsignee"]=>
	array(7) {
	  ["PersonName"]=>
	  string(0) ""
	  ["Mobile"]=>
	  string(0) ""
	  ["EmailAddress"]=>
	  string(0) ""
	  ["LineAddress"]=>
	  string(0) ""
	  ["CityName"]=>
	  string(0) ""
	  ["PostalCode"]=>
	  string(0) ""
	  ["CountryCode"]=>
	  string(0) ""
	}
	["ProductId"]=>
	array(1) {
	  [0]=>
	  array(2) {
		["id"]=>
		string(3) "154"
		["Quantity"]=>
		string(1) "1"
	  }
	}
  }
  ';

$curl = curl_init();
curl_setopt_array($curl, array(
		CURLOPT_URL => "http://payapi.createkwservers.com/api/index.php",
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => $postData,
		CURLOPT_HTTPHEADER => array("Authorization: Bearer $token","Content-Type: application/json"),
	));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
?>
