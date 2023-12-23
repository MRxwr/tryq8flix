<?php
   
	$data['data']=$apidata['token'];
	if($apidata['token']){
	    $token =$apidata['token'];
	    if($apidata['mode']=='live'){
	       $basURL = "https://api.myfatoorah.com"; 
	    }else{
	       $basURL = "https://apitest.myfatoorah.com";
		   $token = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
	    }


		if(isset($_POST['Key'])){ $Key = $_POST['Key']; }else{ $Key =''; }
		if(isset($_POST['KeyType'])){ $KeyType = $_POST['KeyType']; }else{ $KeyType =''; }
		
	$postData='{
        "Key":"'.$Key.'",
        "KeyType": "'.$KeyType.'",
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
		CURLOPT_URL => "$basURL/v2/getPaymentStatus",
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
			$InvoiceStatus = $json["Data"]["InvoiceStatus"];
			$InvoiceId = $json["Data"]["InvoiceId"];
			if(isset($InvoiceStatus) && isset($InvoiceId) ){
				$data['status']=200;
				$data['type']='success';
				$data['msg']='payment Status!!'; 
				$data['data'] = $json;
				echo json_encode($data); 
			}else{
				$data['status']=200;
				$data['type']='error';
				$data['msg']='Invalid Status data !!'; 
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