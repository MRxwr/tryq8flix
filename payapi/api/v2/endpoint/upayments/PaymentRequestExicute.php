<?php
 // echo json_encode($apidata);exit;
	$data['data']=$apidata['uMerchantID'];
	if($apidata['uMerchantID']){
	   
	    if($apidata['mode']=='live'){
	       $basURL = "https://api.upayments.com/payment-reques"; 
	    }else{
	       $basURL = "https://api.upayments.com/test-payment";
	    }
		
		if(isset($_POST['PaymentMethodId'])){
	        $PaymentMethodId = $_POST['PaymentMethodId']; 
	    }else{ 
			$PaymentMethodId ='';
				 $validation[$key]['field']='PaymentMethodId';
				 $validation[$key]['msg']='Payment MethodId is required!';
				 $key++;
		}

		if(isset($_POST['OrderId'])){
	        $OrderId = $_POST['OrderId']; 
	    }else{ 
			$OrderId ='';
				 $validation[$key]['field']='OrderId';
				 $validation[$key]['msg']='Payment OrderId is required!';
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
		if(isset($_POST['ProductName'])){ $ProductName		= $_POST['ProductName']; }else{ $ProductName =''; }
		if(isset($_POST['ProductQty'])){ $ProductQty		= $_POST['ProductQty']; }else{ $ProductQty =''; }
		if(isset($_POST['ProductPrice'])){ $ProductPrice	= $_POST['ProductPrice']; }else{ $ProductPrice =''; }
		$validation=array();
		$key=0;
	
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
		$OrderId =	getInvoiceId(16);
	  ####### Execute Payment ######
		
		$fields['merchant_id'] = $apidata['uMerchantID'];
		$fields['username'] = $apidata['username'];
		$fields['password'] = stripslashes($apidata['password']);
		$fields['api_key'] = $apidata['uApikey'];
		
		$fields['order_id'] = $OrderId;
		$fields['total_price'] = $InvoiceValue;
		$fields['CurrencyCode'] = $DisplayCurrencyIso;
		$fields['CstFName'] = $CustomerName;
		$fields['CstEmail'] = $CustomerEmail;
		$fields['CstMobile'] = $CustomerMobile;
		$fields['success_url'] = $CallBackUrl;
		$fields['error_url'] = $ErrorUrl;
		if($apidata['mode']!='live'){
			$fields['test_mode'] = 1;// test mode enabled
		}
		if($apidata['mode']=='live'){
			$fields['whitelabled'] = true; // only accept in live credentials (it will not work in test)
		}
		$fields['payment_gateway'] = $PaymentMethodId; // only worksin production mode
		$fields['ProductName'] = json_encode($ProductName);
		$fields['ProductQty'] = json_encode($ProductQty);
		$fields['ProductPrice'] = json_encode($ProductPrice);
		$fields['reference'] = $CustomerReference;

	//    $data['status']=200;
	//    $data['type']='error';
	//    $data['msg']='payment error !!'; 
	//    $data['data'] =$fields;
	//    print_r($fields);
	//    exit;

		$fields_string = http_build_query($fields);
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,"$basURL");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);curl_close($ch);
		$err = curl_error($curl);
		//$server_output = json_decode($server_output,true);
		//window.location.href=$server_output[‘paymentURL’]; // javascript
		//header(‘Location:’.$server_output[‘paymentURL’]); // PHP

		
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
			$payment_url = $json["paymentURL"];
			$request_status = $json["status"];
			if(isset($request_status) && $request_status == 'success' ){
				$data['status']=200;
				$data['type']='success';
				$data['msg']='Invoice Created Successfully!'; 
				$data['data'] = $json;
				$data['data']['PaymentURL'] = $payment_url;
				$data['data']['InvoiceId'] = $OrderId;
				echo json_encode($data); 
			}else{
				$data['status']=200;
				$data['type']='error';
				$data['msg']='Invalid payment data !!'; 
				$data['data'] = $json;
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