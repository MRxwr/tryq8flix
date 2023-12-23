<?php
	$data['data']=$apidata['token'];
	if($apidata['token']){
	    $token =$apidata['token'];
	    if($apidata['mode']=='live'){
	       $basURL = "https://api.myfatoorah.com"; 
	    }else{
	       $basURL = "https://apitest.myfatoorah.com";
	    }
        $validation=array();
		$key=0;
		if(isset($_POST['ShippingMethod'])){
			 $ShippingMethod = $_POST['ShippingMethod'];
			 }else{ 
				 $ShippingMethod ='';
				 $validation[$key]['field']='ShippingMethod';
				 $validation[$key]['msg']='Shipping Method is required!';
				 $key++;
			 }
		if(isset($_POST['InvoiceNumbers'])){ $InvoiceNumbers = $_POST['InvoiceNumbers']; }else{ $InvoiceNumbers =''; }
		if(isset($_POST['OrderStatusChangedTo'])){ $OrderStatusChangedTo = $_POST['OrderStatusChangedTo']; }else{
			 $OrderStatusChangedTo =''; 
			
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
        "ShippingMethod":"'.$ShippingMethod.'",';
		if(!empty( $InvoiceNumbers)){
			$postData .='"InvoiceNumbers":'.json_encode($InvoiceNumbers).',';
		}
	    $postData .='"OrderStatusChangedTo": '.$OrderStatusChangedTo.',
      }';

	 
	
	  ####### Execute Payment ######
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => "$basURL/v2/UpdateShippingStatus",
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
			$data['msg']='Update Shipping Status error !!'; 
			$data['data'] =$err;
			echo json_encode($data);
		}else {
		//echo "$response '<br />'";
			$json = json_decode($response, true);
			$IsSuccess= $json["IsSuccess"];
			if(isset($IsSuccess)){
				$data['status']=200;
				$data['type']='success';
				$data['msg']='Update Shipping Status!!'; 
				$data['data'] = $json;
				echo json_encode($data); 
			}else{
				$data['status']=200;
				$data['type']='error';
				$data['msg']='Invalid refund data !!'; 
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