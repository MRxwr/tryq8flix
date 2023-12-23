<?php
   
	$data['data']=$apidata['token'];
	if($apidata['api_key']){
	    $token =$apidata['token'];
	    if($apidata['mode']=='live'){
	       $basURL = "https://api.myfatoorah.com"; 
	    }else{
	       $basURL = "https://apitest.myfatoorah.com";
		   $token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
	    }
		
		if(isset($_POST['PaymentMethodId'])){
	        $PaymentMethodId = $_POST['PaymentMethodId']; 
	    }else{ 
			$PaymentMethodId ='';
				 $validation[$key]['field']='PaymentMethodId';
				 $validation[$key]['msg']='Payment MethodId is required!';
				 $key++;
		}

		if(isset($_POST['DisplayCurrencyIso'])){ $DisplayCurrencyIso = $_POST['DisplayCurrencyIso']; }else{ $DisplayCurrencyIso ='KWD'; }
		if(isset($_POST['MobileCountryCode'])){ $MobileCountryCode = $_POST['MobileCountryCode']; }else{ $MobileCountryCode ='+965'; }
		if(isset($_POST['InvoiceValue'])){
	        $InvoiceValue = $_POST['InvoiceValue']; 
	    }else{ 
			$InvoiceValue ='';
				 $validation[$key]['field']='InvoiceValue';
				 $validation[$key]['msg']='Invoice Value is required!';
				 $key++;
		}
		if(isset($_POST['CallBackUrl'])){ $CallBackUrl= $_POST['CallBackUrl']; }else{ $CallBackUrl =''; }
		if(isset($_POST['ErrorUrl'])){ $ErrorUrl= $_POST['ErrorUrl']; }else{ $ErrorUrl =''; }
		if(isset($_POST['CustomerAddress'])){ $CustomerAddress= $_POST['CustomerAddress']; }else{ $CustomerAddress =''; }
		if(isset($_POST['ShippingConsignee'])){ $ShippingConsignee= $_POST['ShippingConsignee']; }else{ $ShippingConsignee =''; }
		if(isset($_POST['InvoiceItems'])){ $InvoiceItems= $_POST['InvoiceItems']; }else{ $InvoiceItems =''; }
		$validation=array();
		$key=0;
		if(isset($apidata['ShippingMethod'])){
			 $ShippingMethod = $apidata['ShippingMethod'];
			 }else{ 
				 $ShippingMethod ='';
				 $validation[$key]['field']='ShippingMethod';
				 $validation[$key]['msg']='Shipping Method is required!';
				 $key++;
		}
		if(isset($_POST['CustomerName'])){ $CustomerName = $_POST['CustomerName']; }else{
			 $CityName =''; 
			 $validation[$key]['field']='CustomerName';
			 $validation[$key]['msg']='Customer Name is required!';
			 $key++;
			}
		if(isset($_POST['CustomerMobile'])){ $CustomerMobile = $_POST['CustomerMobile']; }else{ 
			$PostalCode =''; 
			 $validation[$key]['field']='CustomerMobile';
			 $validation[$key]['msg']='Customer Mobile is required!';
			 $key++;
		}
		if(isset($_POST['CustomerEmail'])){ $CustomerEmail = $_POST['CustomerEmail']; }else{ 
			$PostalCode =''; 
			 $validation[$key]['field']='CustomerEmail';
			 $validation[$key]['msg']='Customer Email is required!';
			 $key++;
		}
		if(isset($apidata['CustomerReference'])){ $CustomerReference = $apidata['CustomerReference']; }else{
			 $CustomerReference =''; 
			 $validation[$key]['field']='CustomerReference';
			 $validation[$key]['msg']='Customer Reference is required!';
			 $key++;
			}
		if ( isset($_POST['SupplierCode']) || $_POST['SupplierCode'] != 0 ){
			$SupplierCode = $_POST['SupplierCode'];
		}else{
			$SupplierCode = $apidata['SupplierCode'];
		}
		
		if ( isset($_GET['ClubId']) || !empty($_GET['ClubId']) ){
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => "dabbirnyapi.create-kw.com/api/apiprovider/ClubDetails/{$_GET['ClubId']}?lang=en",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
			'API_KEY: APIKEY'
			),
			));
			$responseDabb = curl_exec($curl);
			$responseDabb = json_decode($responseDabb,true);
			curl_close($curl);
			$SupplierCode = $responseDabb["Respponse"]["SupplierCode"];
			$CustomerReference = "Ref{$_GET['ClubId']}";
		}
		
		if ( isset($_POST['SendMoney']) || $_POST['SendMoney'] != 0 ){
			$InvoiceValue = $InvoiceValue - ($InvoiceValue*$_POST['Percentage']/100) + 0.25;
			$InvoiceValue = round($InvoiceValue,2);
			$InvoiceItems[0]['UnitPrice'] = $InvoiceValue;
		}
		
		
		if(!empty($validation)){
			$data['status']=200;
			$data['type']='error';
			$data['msg']='validation error !!'; 
			$data['data'] =$validation;
			echo json_encode($data);
			exit;
		}
		
	$postData='{
        "PaymentMethodId":"'.$PaymentMethodId.'",
        "CustomerName": "'.$CustomerName.'",
        "DisplayCurrencyIso": "'.$DisplayCurrencyIso.'",
        "MobileCountryCode":"'.$MobileCountryCode.'",
        "CustomerMobile": "'.$CustomerMobile.'",
        "CustomerEmail": "'.$CustomerEmail.'",
        "InvoiceValue": '.$InvoiceValue.',
        "CallBackUrl": "'.$CallBackUrl.'",
        "ErrorUrl": "'.$ErrorUrl.'",
        "Language": "en",
        "CustomerReference" :"'.$CustomerReference.'",
		"CustomerAddress": '.json_encode($CustomerAddress).',
        "ExpireDate": "",
		"SupplierCode": '.$SupplierCode.',
        "InvoiceItems":'.json_encode($InvoiceItems).',';

		if($ShippingMethod >0){
$postData .='"ShippingMethod":'.$ShippingMethod.',
			"ShippingConsignee": '.json_encode($ShippingConsignee).',';
		}
 $postData .='"SourceInfo": "string"
       }';

	   //$data['status']=200;
	   //$data['type']='error';
	   //$data['msg']='payment error !!'; 
	   //$data['data'] =$postData;
	   //print_r($postData);
	   //exit;
	
	  ####### Execute Payment ######
	  $appdata=array(
	      'baseurl'=>$basURL,
	      'postdata'=>$postData,
	      'token'=>$token,
	      'point'=>'ExecutePayment',
	      );
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://myfatoorah.tryq8flix.com/index.php",
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => json_encode($appdata),
	
		));
		
// 		curl_setopt_array($curl, array(
// 		CURLOPT_URL => "$basURL/v2/ExecutePayment",
// 		CURLOPT_CUSTOMREQUEST => "POST",
// 		CURLOPT_POSTFIELDS => $postData,
// 		CURLOPT_HTTPHEADER => array("Authorization: Bearer $token","Content-Type: application/json"),
// 		));
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);
		//var_dump($response); exit;
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			$data['status']=200;
			$data['type']='error';
			$data['msg']='payment error !!'; 
			$data['data'] =$err;
			echo json_encode($data);
		}else {
			//"$response '<br />'";
			$json = json_decode($response, true);
			$payment_url = $json["Data"]["PaymentURL"];
			$InvoiceId = $json["Data"]["InvoiceId"];
			if(isset($payment_url) && isset($InvoiceId) ){
				$pdar=array('PaymentURL'=>$payment_url, 'InvoiceId'=>$InvoiceId,'pjson'=>$json);
				$data['status']=200;
				$data['type']='success';
				$data['msg']='payment successfully done!!'; 
				$data['data'] = $pdar;
				echo json_encode($data); 
			}else{
				$data['status']=200;
				$data['type']='error';
				$data['msg']='Invalid payment data !!'; 
				$data['data'] = $json;
				$data['data']['InvoiceId'] = '123';
				$data['data']['PaymentURL'] = ''; 
				echo json_encode($data);
			}
		}
      
	}else{
	    	$data['status']=200;
        	$data['type']='error';
        	$data['msg']='Invalied the Myfatoorah token !!'; 
        	$data['data'] = array();
        	echo json_encode($data);
	}
    
    
?>