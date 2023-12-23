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
		if(isset($_POST['shippingMethod'])){ $shippingMethod = $_POST['shippingMethod']; }else{ 
            $ShippingMethod ='';
            $validation[$key]['field']='shippingMethod';
            $validation[$key]['msg']='Refund Charge On Customer is required!';
            $key++;
        }
		if(isset($_POST['orderStatus'])){ $orderStatus = $_POST['orderStatus']; }else{ 
            $orderStatus ='';
            $validation[$key]['field']='orderStatus';
            $validation[$key]['msg']='order Status is required!';
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
		

	  ####### Execute Payment ######
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => "$basURL//v2/GetShippingOrderList?shippingMethod=".$shippingMethod."&orderStatus=".$orderStatus,
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
			$data['msg']='Request Pickup error !!'; 
			$data['data'] =$err;
			echo json_encode($data);
		}else {
			$json = json_decode($response, true);
			$IsSuccess= $json["IsSuccess"];
			if(isset($IsSuccess)){
				$data['status']=200;
				$data['type']='success';
				$data['msg']='Request Pickup Status!!'; 
				$data['data'] = $json;
				echo json_encode($data); 
			}else{
				$data['status']=200;
				$data['type']='error';
				$data['msg']='Request Pickup data !!'; 
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