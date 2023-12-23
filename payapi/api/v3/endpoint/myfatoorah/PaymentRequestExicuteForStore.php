 <?php
   require_once 'library/PaymentMyfatoorahApiV2.php';
	$data['data']=$apidata['token'];
	if($apidata['api_key']){
	    
	    if($apidata['mode']=='live'){
	       $basURL = "https://api.myfatoorah.com"; 
		   $token = $apidata['token'];
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
		//if(isset($_POST['InvoiceValue'])){ $InvoiceValue = $_POST['InvoiceValue']; }else{ $InvoiceValue =''; }
		
		if(isset($_POST['CallBackUrl'])){ $CallBackUrl= $_POST['CallBackUrl']; }elseif(isset($apidata['SiteUrl'])){ $CallBackUrl= $apidata['SiteUrl'].'/details'; }else{ $CallBackUrl =''; }
		
		if(isset($_POST['ErrorUrl'])){ $ErrorUrl= $_POST['ErrorUrl']; }elseif(isset($apidata['SiteUrl'])){ $ErrorUrl= $apidata['SiteUrl'].'/checkout2.php?status=fail'; }else{ $ErrorUrl =''; }
		
		if(isset($_POST['CustomerAddress'])){ $CustomerAddress= $_POST['CustomerAddress']; }else{ $CustomerAddress =''; }
		if(isset($_POST['ShippingConsignee'])){ $ShippingConsignee= $_POST['ShippingConsignee']; }else{ $ShippingConsignee =''; }
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
		if(isset($apidata['SupplierCode'])){
			$SupplierCode = $apidata['SupplierCode'];
			}else{ 
				$SupplierCode ='';
				$validation[$key]['field']='SupplierCode';
				$validation[$key]['msg']='Supplier Code is required!';
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
				// $data['status']=200;
				// $data['type']='error';
				// $data['msg']='payment error !!'; 
				// $data['data'] =$quantity;
				// echo json_encode($data);
				// exit;
				 $lists = implode(',', $items);
				// $data['data'] =$lists;
				// echo json_encode($data);
				// exit;
				$key=0;
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
											//var_dump($row2s['percentage']);
											 $discount = (intval($price)  * (intval($row2s['percentage'])/100));
										}else if ($row['discount']>0){
											$discount = (intval($price) * (intval($row['discount'])/100));
										}
									}
									if($discount>0){
										$price = $price -$discount;
									}
									
									$InvoiceItems[$key]['ItemName']= $row['enTitle'];
									$InvoiceItems[$key]['Quantity']=$quantity[$row['sid']];
									$InvoiceItems[$key]['UnitPrice']= $price;
									$InvoiceItems[$key]['Weight']= $row['weight'];
									$InvoiceItems[$key]['Height']= $row['height'];
									$InvoiceItems[$key]['Width']= $row['width'];
									$InvoiceItems[$key]['Depth']= $row['depth'];
									$InvoiceValue = $InvoiceValue + ($price * $quantity[$row['sid']]);
									$Invoice2Items[$key]['ProductName']= $row['enTitle'];
									$Invoice2Items[$key]['Description']= $row['enTitle'];
									$Invoice2Items[$key]['Quantity']=$quantity[$row['sid']];
									$Invoice2Items[$key]['UnitPrice']= $price;
									$Invoice2Items[$key]['Weight']= $row['weight'];
									$Invoice2Items[$key]['Height']= $row['height'];
									$Invoice2Items[$key]['Width']= $row['width'];
									$Invoice2Items[$key]['Depth']= $row['depth'];
									
									//$InvoiceItems[$key]['InvoiceValue']  = $InvoiceValue;
									$key++;
									
									 
							}
							// Free result set
							$data['msg']='subproduct!!'; 
							mysqli_free_result($result);
						} else{
							echo "No records matching your query were found.";
						}
					} 
				
				// Close connection
				mysqli_close($link);	

	}else{
		$InvoiceValue=$_POST['invoiceValue'];
	}
	if(isset($_POST['Delivery'])){
		if($_POST['Delivery']>0){ 
			$Delivery = $_POST['Delivery'];
			$InvoiceValue = $InvoiceValue + $Delivery;
			$InvoiceItems[$key]['ItemName']= 'Delivery Charges';
			$InvoiceItems[$key]['Quantity']='1';
			$InvoiceItems[$key]['UnitPrice']= $Delivery;
			//$InvoiceItems[$key+1]['InvoiceValue']  = $InvoiceValue;
			$InvoiceValue =	number_format((float)$InvoiceValue, 3, '.', '');
			$key++;
		}elseif($_POST['Delivery']<=0 ){ 
			$post2Data='{
				"ShippingMethod":"'.$ShippingMethod.'",
				"Items":'.json_encode($Invoice2Items).',
				"CityName": "'.$ShippingConsignee['CityName'].'",
				"PostalCode": "'.$ShippingConsignee['PostalCode'].'",
				"CountryCode": "'.$ShippingConsignee['CountryCode'].'",
			}';
			
				####### Execute Payment ######
				$curl = curl_init();
				curl_setopt_array($curl, array(
				CURLOPT_URL => "$basURL/v2/CalculateShippingCharge",
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $post2Data,
				CURLOPT_HTTPHEADER => array("Authorization: Bearer $token","Content-Type: application/json"),
				));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);
				if ($err) {
					$data['status']=200;
					$data['type']='error';
					$data['msg']='Make refund error !!'; 
					$data['data'] =$err;
					//echo json_encode($data);
				}else {
				//echo "$response '<br />'";
					$json = json_decode($response, true);
					//var_dump($json);
					$IsSuccess= $json["IsSuccess"];
					if(isset($IsSuccess)){
						
						$Delivery = ($json['Data']['Fees']?$json['Data']['Fees']:0.00);
						$InvoiceValue = $InvoiceValue + $Delivery;
						$InvoiceItems[$key]['ItemName']= 'Delivery Charges';
						$InvoiceItems[$key]['Quantity']='1';
						$InvoiceItems[$key]['UnitPrice']= $Delivery;
						//$InvoiceItems[$key+1]['InvoiceValue']  = $InvoiceValue;
						$InvoiceValue =	number_format((float)$InvoiceValue, 3, '.', '');
						$key++;
					}
				}		
		   }
	  }

    if($apidata['visamaster_deduction']==1 && $PaymentMethodId == '2' ){
		$visa_tax = ($InvoiceValue*($apidata['visamaster_charges']/100));
		$InvoiceValue = $InvoiceValue + ($InvoiceValue*($apidata['visamaster_charges']/100));
		$InvoiceItems[$key]['ItemName']= 'VISA/MASTER TAX';
		$InvoiceItems[$key]['Quantity']='1';
		$InvoiceItems[$key]['UnitPrice']= $visa_tax;
		//$InvoiceItems[$key]['InvoiceValue']  = $InvoiceValue;
		$InvoiceValue =	number_format((float)$InvoiceValue, 3, '.', '');
		$key++;
	}else{
		$InvoiceValue =	number_format((float)$InvoiceValue, 3, '.', '');	
	}
	
	

	// $data['status']=200;
	// $data['type']='error';
    // $data['msg']='payment error !!'; 
	// $data['data'] =$InvoiceItems;
	// echo json_encode($data);
	// exit;

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
		if($ShippingMethod >0 && $_POST['Delivery'] <= 0){
$postData .='"ShippingMethod":'.$ShippingMethod.',
			"ShippingConsignee": '.json_encode($ShippingConsignee).',';
		}
		
 $postData .= '"SourceInfo": "string"
			}';

	//   $data['status']=200;
	//   $data['type']='error';
	//   $data['msg']='payment error !!'; 
	//   $data['data'] =$postData;
	//   echo json_encode($data);
	//   exit;
	//   echo json_encode($postData);die();
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
			$data['status']=200;
			$data['type']='error';
			$data['msg']='myfatoorah error !!'; 
			$data['data'] =$err;
			echo json_encode($data);
		}else {
		//echo "$response '<br />'";
			$json = json_decode($response, true);
			$payment_url = $json["Data"]["PaymentURL"];
			$InvoiceId = $json["Data"]["InvoiceId"];
			if(isset($payment_url) && isset($InvoiceId) ){
				$pdar=array('PaymentURL'=>$payment_url, 'InvoiceId'=>$InvoiceId,'pjson'=>$json);
				$data['status']=200;
				$data['type']='success';
				$data['msg']='Invoice Created Successfully!'; 
				$data['data'] = $pdar;
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