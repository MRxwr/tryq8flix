<?php
   
	$data['data']=$apidata['token'];
	if($apidata['token']){
	    $token =$apidata['token'];
	    if($apidata['mode']=='live'){
	       $basURL = "https://api.myfatoorah.com"; 
	    }else{
	       $basURL = "https://apitest.myfatoorah.com";
	    }
		if(isset($_POST['shippingMethod'])){ $shippingMethod = $_POST['shippingMethod']; }else{ $shippingMethod =''; }
		if(isset($_POST['countryCode'])){ $countryCode = $_POST['countryCode']; }else{ $countryCode =''; }
		if(isset($_POST['searchValue'])){ $searchValue = $_POST['searchValue']; }else{ $searchValue =''; }
		
	$postData='{
        "shippingMethod":"'.$shippingMethod.'",
        "countryCode": "'.$countryCode.'",
		"searchValue": '.$searchValue.'
      }';

	  ####### Execute Payment ######
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => "$basURL//v2/Getcities?shippingMethod=".$shippingMethod."&countryCode=".$countryCode."&searchValue=".$searchValue,
		CURLOPT_CUSTOMREQUEST => "GET",
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
			$json = json_decode($response, true);
			$IsSuccess= $json["IsSuccess"];
			if(isset($IsSuccess)){
				$data['status']=200;
				$data['type']='success';
				$data['msg']='Get Cities Status!!'; 
				$data['data'] = $json;
				echo json_encode($data); 
			}else{
				$data['status']=200;
				$data['type']='error';
				$data['msg']='Invalid Cities data !!'; 
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