 <?php
   
	$data['data']=$apidata['token'];
	if($apidata['token']){
	    $token =$apidata['token'];
	    if($apidata['mode']=='live'){
	       $basURL = "https://api.myfatoorah.com"; 
	    }else{
	       $basURL = "https://apitest.myfatoorah.com";
	    }
		
	    if(isset($_POST['PaymentMethodId'])){
	        $PaymentMethodId = $_POST['PaymentMethodId']; 
	    }else{ $PaymentMethodId =1; }
		if(isset($_POST['CustomerName'])){ $CustomerName = $_POST['CustomerName']; }else{ $CustomerName =''; }
		if(isset($_POST['DisplayCurrencyIso'])){ $DisplayCurrencyIso = $_POST['DisplayCurrencyIso']; }else{ $DisplayCurrencyIso ='KWD'; }
		if(isset($_POST['MobileCountryCode'])){ $MobileCountryCode = $_POST['MobileCountryCode']; }else{ $MobileCountryCode ='+965'; }
		if(isset($_POST['CustomerMobile'])){ $CustomerMobile = $_POST['CustomerMobile']; }else{ $CustomerMobile =''; }
		if(isset($_POST['CustomerEmail'])){ $CustomerEmail = $_POST['CustomerEmail']; }else{ $CustomerEmail =''; }
		if(isset($_POST['InvoiceValue'])){ $InvoiceValue = $_POST['InvoiceValue']; }else{ $InvoiceValue =''; }
		// if(isset($_POST['CallBackUrl'])){ $CallBackUrl= $_POST['CallBackUrl']; }else{ $CallBackUrl =''; }
		// if(isset($_POST['ErrorUrl'])){ $ErrorUrl= $_POST['ErrorUrl']; }else{ $ErrorUrl =''; }
		// if(isset($_POST['CustomerReference'])){ $CustomerReference= $_POST['CustomerReference']; }else{ $CustomerReference =''; }
		if(isset($_POST['CustomerAddress'])){ $CustomerAddress= $_POST['CustomerAddress']; }else{ $CustomerAddress =''; }
		// if(isset($_POST['ShippingMethod'])){ $ShippingMethod= $_POST['ShippingMethod']; }else{ $ShippingMethod =''; }
		// if(isset($_POST['InvoiceItems'])){ $InvoiceItems= $_POST['InvoiceItems']; }else{ $InvoiceItems =''; }
		if(isset($apidata['db_user']) && isset($apidata['db_pass'])  && isset($apidata['db_name']) && !empty($_POST['ProductId'])){
		/* Attempt MySQL server connection. Assuming you are running MySQL
		server with default setting (user 'root' with no password) */
		$link = mysqli_connect($apidata['db_host'], $apidata['db_user'],$apidata['db_pass'],$apidata['db_name']);
		
		// Check connection
		if($link === false){
			die("ERROR: Could not connect. " . mysqli_connect_error());
		}
		$products=array();
		// Attempt select query execution
		$sql = "SELECT * FROM products";
		if($result = mysqli_query($link, $sql)){
			if(mysqli_num_rows($result) > 0){
				$key=0;
				while($row = mysqli_fetch_array($result)){
					if(in_array($row['id'],$_POST['ProductId'])){
					$products[$key]['ItemName']= $row['enTitle'];
					$products[$key]['Quantity']= 1;
					$products[$key]['UnitPrice']= $row['price'];
					$products[$key]['Weight']= $row['weight'];
					$products[$key]['Height']= $row['height'];
					$products[$key]['Width']= $row['width'];
					$products[$key]['Depth']= $row['depth'];
					$key++;
					}
				}
				// Free result set
				
				mysqli_free_result($result);
			} else{
				echo "No records matching your query were found.";
			}
		} else{
			echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
		}
		//var_dump($products);
		// Close connection
		mysqli_close($link);

		//if(!empty($validation)){
			$data['status']=200;
			$data['type']='error';
			$data['msg']='validation error !!'; 
			$data['data'] =$products;
			echo json_encode($data);
			exit;
		//}

	}
	exit;

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
        "ExpireDate": "",
		"SupplierCode": '.$ShippingMethod.',
        "InvoiceItems":'.json_encode($InvoiceItems).'
      }';

	//   $data['status']=200;
	//   $data['type']='error';
	//   $data['msg']='payment error !!'; 
	//   $data['data'] =$_POST['InvoiceItems'];
	//   echo json_encode($data);
	//   exit;
	
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
			$data['msg']='payment error !!'; 
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
				$data['msg']='payment successfully done!!'; 
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