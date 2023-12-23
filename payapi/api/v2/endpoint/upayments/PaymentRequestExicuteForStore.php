<?php
  //echo json_encode($apidata);exit;
	$data['data']=$apidata['uMerchantID'];
	
    
	if($apidata['uMerchantID']){
	   
	    if($apidata['mode']=='live'){
	       $basURL = "https://api.upayments.com/payment-request"; 
	    }else{
	       $basURL = "https://api.upayments.com/test-payment";
	    }
		$validation=array();
		$key=0;
		if(isset($_POST['PaymentMethodId'])){
			if( $_POST['PaymentMethodId'] == 1 ){
				$_POST['PaymentMethodId'] = "knet";
			}else{
				$_POST['PaymentMethodId'] = "cc";
			}
	        $PaymentMethodId = $_POST['PaymentMethodId']; 
	    }else{ 
			$PaymentMethodId ='';
				 $validation[$key]['field']='PaymentMethodId';
				 $validation[$key]['msg']='Payment MethodId is required!';
				 $key++;
		}
		
		

		if(isset($_POST['DisplayCurrencyIso'])){ $DisplayCurrencyIso = $_POST['DisplayCurrencyIso']; }else{ $DisplayCurrencyIso ='KWD'; }
		if(isset($_POST['MobileCountryCode'])){ $MobileCountryCode = $_POST['MobileCountryCode']; }else{ $MobileCountryCode ='+965'; }
		
// 		if(isset($_POST['CallBackUrl'])){ $CallBackUrl= $_POST['CallBackUrl']; }else{ $CallBackUrl =''; }
// 		if(isset($_POST['ErrorUrl'])){ $ErrorUrl= $_POST['ErrorUrl']; }else{ $ErrorUrl =''; }

		if(isset($_POST['CallBackUrl'])){
			$CallBackUrl= $_POST['CallBackUrl'];
		}elseif(isset($apidata['SiteUrl'])){
			$CallBackUrl= $apidata['SiteUrl'].'/details.php';
		}else{
			$CallBackUrl ='';
		}
		
		if(isset($_POST['ErrorUrl'])){
			$ErrorUrl= $_POST['ErrorUrl'];
		}elseif(isset($apidata['SiteUrl'])){
			$ErrorUrl= $apidata['SiteUrl'].'/checkout.php?status=fail';
		}else{
			$ErrorUrl ='';
		}
		
		//$CallBackUrl =$apidata['SiteUrl'];
		//$ErrorUrl = $apidata['SiteUrl'];
// 		if(isset($_POST['ProductName'])){ $ProductName		= $_POST['ProductName']; }else{ $ProductName =''; }
// 		if(isset($_POST['ProductQty'])){ $ProductQty		= $_POST['ProductQty']; }else{ $ProductQty =''; }
// 		if(isset($_POST['ProductPrice'])){ $ProductPrice	= $_POST['ProductPrice']; }else{ $ProductPrice =''; }
// 		if(isset($_POST['extraMerchantsData'])){ $extraMerchantsData	= $_POST['extraMerchantsData']; }else{  $extraMerchantsData =''; }
		
	
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
		$InvoiceItems=array();
		$Invoice2Items=array();
		$InvoiceValue=0;
		$pkey=0;
		if(isset($_POST['invoiceValue'])){
		   if($_POST['invoiceValue']>0){
		        $InvoiceValue =	number_format((float)$_POST['invoiceValue'], 3, '.', '');
		        $ProductName[0]= ($apidata['title_en']?$apidata['title_en']:$apidata['title_ar']). '-cStore';
		        $ProductQty[0]=1;
		        $ProductPrice[0]= $InvoiceValue;
		    }
		   }else {
		   if(isset($apidata['db_user']) && isset($apidata['db_pass'])  && isset($apidata['db_name']) && !empty($_POST['ProductId'])){
				/* Attempt MySQL server connection. Assuming you are running MySQL
				server with default setting (user 'root' with no password) */
				$link = mysqli_connect($apidata['db_host'], $apidata['db_user'],$apidata['db_pass'],$apidata['db_name']);
				
				// Check connection
				if($link === false){
					die("ERROR: Could not connect. " . mysqli_connect_error());
				}
				
				// Attempt select query execution
				//var_dump($_POST['ProductId']);exit;
				// SQL query
				$items=array();
				$quantity=array();
				foreach($_POST['ProductId'] as $k=>$prod){
					$items[]=$prod['id'];
					$quantity[$prod['id']]=$prod['Quantity'];
				}
				
				 $lists = implode(',', $items);
				
				
					$sql = "SELECT sp.id as sid,sp.price as sprice, p.* FROM subproducts sp,products p WHERE sp.productId = p.id AND sp.id IN ($lists)";
					if($result = mysqli_query($link, $sql)){
						if(mysqli_num_rows($result) > 0){
							
							while($row = mysqli_fetch_array($result)){
								//var_dump($row);
								    $discount = 0;
									$id = $row['id'];
									//$InvoiceItems[$key]['id']  = $row['id'];
									$price = $row['sprice'];
									if($id){
										$sq2l = "SELECT * FROM `vouchers` WHERE `productId` = $id AND `hidden`=0 ORDER BY id DESC LIMIT 0, 1";
										$resul2t = mysqli_query($link, $sq2l);
										
										if(mysqli_num_rows($resul2t) > 0){
											$row2s = mysqli_fetch_array($resul2t);
											 $discount = (intval($price)  * (intval($row2s['percentage'])/100));
										}else if ($row['discount']>0){
											$discount = (intval($price) * (intval($row['discount'])/100));
										}
									}
									if($discount>0){
										$price = $price -$discount;
									}
									
									$ProductName[$pkey]= $row['enTitle'];
									$ProductQty[$pkey]=$quantity[$row['sid']];
									$ProductPrice[$pkey]= $price;
									
									$InvoiceValue = $InvoiceValue + ($price * $quantity[$row['sid']]);
									$pkey++;
									
									
							}
							// Free result set
							$data['msg']='subproduct!!'; 
							mysqli_free_result($result);
						} else{
							//echo "No records matching your query were found.";
						}
					} 
				
				// Close connection
				mysqli_close($link);	

	}
    	    if(isset($_POST['Delivery'])){
    		    if($_POST['Delivery']>0){ 
        			$Delivery = $_POST['Delivery'];
        			$InvoiceValue = $InvoiceValue + $Delivery;
        			$ProductName[$pkey]= 'Delivery Charges';
        			$ProductQty[$pkey]=1;
        			$ProductPrice[$pkey]= $Delivery;
        			//$InvoiceItems[$key+1]['InvoiceValue']  = $InvoiceValue;
        			$InvoiceValue =	number_format((float)$InvoiceValue, 3, '.', '');
        			$pkey++;
    		}
    	  }

            if($apidata['visamaster_deduction']==1 && $PaymentMethodId == 'cc' ){
        		$visa_tax = ($InvoiceValue*($apidata['visamaster_charges']/100));
        		$InvoiceValue = $InvoiceValue + ($InvoiceValue*($apidata['visamaster_charges']/100));
        		$ProductName[$pkey]= 'VISA/MASTER TAX';
        		$ProductQty[$pkey]=1;
        		$ProductPrice[$pkey]= $visa_tax;
        		//$InvoiceItems[$key]['InvoiceValue']  = $InvoiceValue;
        		$InvoiceValue =	number_format((float)$InvoiceValue, 3, '.', '');
        		$pkey++;
        	}else{
        		$InvoiceValue =	number_format((float)$InvoiceValue, 3, '.', '');	
        	}
    	  }
	    
	
	  ####### Execute Payment ######
	  $InvoiceValue =	number_format((float)$InvoiceValue, 2, '.', '');
	  $extraMerchantsData = array(
    	'amounts' =>array('0'=>$InvoiceValue) ,
    	'charges' =>array('0'=>number_format((float)($apidata['chargeType']=='fixed'?$apidata['chargeAmount']:($InvoiceValue*($apidata['chargeAmount']/100))), 2, '.', '')) ,
    	'chargeType' =>array('0'=>$apidata['chargeType']) ,
    	'cc_charge' =>array('0'=>number_format((float)($apidata['cc_chargetype']=='fixed'?$apidata['cc_charge']:($InvoiceValue*($apidata['cc_charge']/100))), 2, '.', '')) ,
    	'cc_chargetype' =>array('0'=>$apidata['cc_chargetype']) ,
    	'ibans' =>array('0'=>$apidata['ibanMarchent']) 
    	);
	    $OrderId =	getInvoiceId(16);
		$fields['merchant_id'] = $apidata['uMerchantID'];
		$fields['username'] = $apidata['username'];
		$fields['password'] = stripslashes($apidata['password']);
		$fields['api_key'] = password_hash($apidata['uApikey'],PASSWORD_BCRYPT);
		
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
		/*
		$fields['ProductName'] = "cart Id {$OrderId}";
		$fields['ProductQty'] = 1;
		$fields['ProductPrice'] = $InvoiceValue; sent by api
		*/
		$fields['ProductName'] = json_encode($ProductName);
		$fields['ProductQty'] = json_encode($ProductQty);
		$fields['ProductPrice'] = json_encode($ProductPrice);
		$fields['ExtraMerchantsData'] = json_encode($extraMerchantsData);
		$fields['reference'] = $CustomerReference;

	   // $data['status']=200;
	   // $data['type']='error';
	   // $data['msg']='payment error !!'; 
	   // $data['data'] =$fields;
	   // print_r($fields);
	   // exit;
	   
		$fields_string = http_build_query($fields);
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,"$basURL");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);curl_close($ch);
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
			//var_dump($json);
			$payment_url = $json["paymentURL"];
			$request_status = $json["status"];
			if(isset($request_status) && $request_status == 'success' ){
				$data['status']=200;
				$data['type']='success';
				$data['msg']='payment successfully done!!'; 
				$data['data'] = $json;
				$data['data']['PaymentURL'] = $payment_url;
				$data['data']['InvoiceId'] = $OrderId;
				//$data['data']['fields'] = $fields;
				echo json_encode($data); 
			}else{
				$data['status']=200;
				$data['type']='error';
				$data['msg']='Invalid payment data null !!'; 
				$data['data'] = $json;
				echo json_encode($data);
			}
		}
      
	}else{
	    	$data['status']=200;
        	$data['type']='error';
        	$data['msg']='Invalied the uPayment token !!'; 
        	$data['data'] = array();
        	echo json_encode($data);
	}
    
?>