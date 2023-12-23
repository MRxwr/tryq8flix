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