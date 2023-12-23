<?php
 //echo json_encode($apidata);exit;
	$data['data']=$apidata['MerchantID'];
	if($apidata['MerchantID']){
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
		// if(isset($_POST['ProductName'])){ $ProductName		= $_POST['ProductName']; }else{ $ProductName =''; }
		// if(isset($_POST['ProductQty'])){ $ProductQty		= $_POST['ProductQty']; }else{ $ProductQty =''; }
		// if(isset($_POST['ProductPrice'])){ $ProductPrice	= $_POST['ProductPrice']; }else{ $ProductPrice =''; }
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
		
	  ####### Execute Payment ######
	

	//    $data['status']=200;
	//    $data['type']='error';
	//    $data['msg']='payment error !!'; 
	//    $data['data'] =$fields;
	//    print_r($fields);
	//    exit;
	//ini_set("display_errors", "1");
	//error_reporting(E_ALL);
	include_once("bookeey.php");
	$bookeeyPipe = new bookeey;
	$isEnable = $bookeeyPipe->isEnable();
	if($apidata['mode']!='live'){
		$bookeeyPipe->setIsTestModeEnable(1);
	}
	if($isEnable){
		/* $bookeeyPipe->setTitle('Custom Title');
		$bookeeyPipe->setDescription('Custom Description'); */
		$bookeeyPipe->setSuccessUrl($CallBackUrl);
		$bookeeyPipe->setFailureUrl( $ErrorUrl);
		$bookeeyPipe->setMerchantID($apidata['MerchantID']);    // Set the Merchant ID
		$bookeeyPipe->setSecretKey($apidata['SecretKey']);    // Set the Secret Key
		$bookeeyPipe->setOrderId($OrderId);  // Set Order ID - This should be unique for each transaction.
		$bookeeyPipe->setAmount($InvoiceValue);  // Set amount in KWD
		$bookeeyPipe->setPayerName($CustomerName);  // Set Payer Name
		$bookeeyPipe->setPayerPhone($CustomerMobile);  // Set Payer Phone Numner
		$bookeeyPipe->setSelectedPaymentOption($PaymentMethodId);
		
			$transactionDetails = array(
				
			);
	
			$respose = $bookeeyPipe->initiatePayment($transactionDetails);
			

			if(isset($respose["PayUrl"]) && $respose["PayUrl"]!=''){
				$data['status']=200;
				$data['type']='success';
				$data['msg']='Invoice Created Successfully!'; 
				$data['data'] = $respose;
				echo json_encode($data); 
			}else{
				$data['status']=200;
				$data['type']='error';
				$data['msg']='Invalid Bookeey payment data !!'; 
				$data['data'] = $respose;
				echo json_encode($data);
			}
			
		
	}else{
		$data['status']=200;
		$data['type']='error';
		$data['msg']='Bookeey Payment not enable !!'; 
		$data['data'] = array();
		echo json_encode($data);
	}
		
	}else{
	    	$data['status']=200;
        	$data['type']='error';
        	$data['msg']='Invalied the Bookeey  token !!'; 
        	$data['data'] = array();
        	echo json_encode($data);
	}
?>