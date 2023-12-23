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
		if(isset($_POST['Items'])){ $Items = $_POST['Items']; }else{ $Items =''; }
		if(isset($_POST['CityName'])){ $CityName = $_POST['CityName']; }else{
			 $CityName =''; 
			 $validation[$key]['field']='CityName';
			 $validation[$key]['msg']='City Name is required!';
			 $key++;
			}
		if(isset($_POST['PostalCode'])){ $PostalCode = $_POST['PostalCode']; }else{ 
			$PostalCode =''; 
			 $validation[$key]['field']='PostalCode';
			 $validation[$key]['msg']='Postal Code is required!';
			 $key++;
		}
		if(isset($_POST['CountryCode'])){ $CountryCode = $_POST['CountryCode']; }else{
			 $CountryCode =''; 
			 $validation[$key]['field']='CountryCode';
			 $validation[$key]['msg']='Country Code is required!';
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
	$postData='{
        "ShippingMethod":"'.$ShippingMethod.'",';
		if(!empty( $Items)){
			$postData .='"Items":'.json_encode($Items).',';
		}
	$postData .='"CityName": "'.$CityName.'",
		"PostalCode": "'.$PostalCode.'",
		"CountryCode": "'.$CountryCode.'",
      }';

	 
	
	  ####### Execute Payment ######
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => "$basURL/v2/CalculateShippingCharge",
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
			$data['msg']='Make refund error !!'; 
			$data['data'] =$err;
			echo json_encode($data);
		}else {
		//echo "$response '<br />'";
			$json = json_decode($response, true);
			$IsSuccess= $json["IsSuccess"];
			if(isset($IsSuccess)){
				$data['status']=200;
				$data['type']='success';
				$data['msg']='Make refund Status!!'; 
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