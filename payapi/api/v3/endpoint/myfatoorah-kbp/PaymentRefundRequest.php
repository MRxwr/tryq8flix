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


		if(isset($_POST['Key'])){ $Key = $_POST['Key']; }else{ $Key =''; }
		if(isset($_POST['KeyType'])){ $KeyType = $_POST['KeyType']; }else{ $KeyType =''; }
		if(isset($_POST['RefundChargeOnCustomer'])){ $RefundChargeOnCustomer = $_POST['RefundChargeOnCustomer']; }else{ $RefundChargeOnCustomer =''; }
		if(isset($_POST['ServiceChargeOnCustomer'])){ $ServiceChargeOnCustomer = $_POST['ServiceChargeOnCustomer']; }else{ $ServiceChargeOnCustomer =''; }
		if(isset($_POST['Amount'])){ $Amount = $_POST['Amount']; }else{ $Amount =''; }
		if(isset($_POST['Comment'])){ $Comment = $_POST['Comment']; }else{ $Comment =''; }
		if(isset($_POST['AmountDeductedFromSupplier'])){ $AmountDeductedFromSupplier = $_POST['AmountDeductedFromSupplier']; }else{ $AmountDeductedFromSupplier =''; }

        $validation=array();
		$key=0;
		if(isset($_POST['Key'])){ $invoiceid = $_POST['Key']; }else{ 
            $invoiceid ='';
            $validation[$key]['field']='Key';
            $validation[$key]['msg']='Shipping Method is required!';
            $key++;
        }
		if(isset($_POST['KeyType'])){ $KeyType = $_POST['KeyType']; }else{ 
            $KeyType ='';
            $validation[$key]['field']='KeyType';
            $validation[$key]['msg']='Key Type as invoiceid is required!';
            $key++;
        }
		if(isset($_POST['RefundChargeOnCustomer'])){ $RefundChargeOnCustomer = $_POST['RefundChargeOnCustomer']; }else{ 
            $RefundChargeOnCustomer ='';
            $validation[$key]['field']='RefundChargeOnCustomer';
            $validation[$key]['msg']='Refund Charge On Customer is required!';
            $key++;
        }
		if(isset($_POST['ServiceChargeOnCustomer'])){ $ServiceChargeOnCustomer = $_POST['ServiceChargeOnCustomer']; }else{ 
            $ServiceChargeOnCustomer ='';
            $validation[$key]['field']='ServiceChargeOnCustomer';
            $validation[$key]['msg']='Service Charge On Customer is required!';
            $key++;
        }
		if(isset($_POST['Amount'])){ $Amount = $_POST['Amount']; }else{ 
            $Amount ='';
            $validation[$key]['field']='Amount';
            $validation[$key]['msg']=' Amount is required!';
            $key++;
        }
		if(isset($_POST['Comment'])){ $Comment = $_POST['Comment']; }else{ $Comment =''; }
		if(isset($_POST['AmountDeductedFromSupplier	'])){ $AmountDeductedFromSupplier	 = $_POST['AmountDeductedFromSupplier	']; }else{ 
            $AmountDeductedFromSupplier	 ='';
            $validation[$key]['field']='AmountDeductedFromSupplier';
            $validation[$key]['msg']='Amount Deducted From Supplier	 is required!';
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
        "Key":"'.$invoiceid.'",
        "KeyType": "'.$KeyType.'",
		"RefundChargeOnCustomer": '.$RefundChargeOnCustomer.',
		"ServiceChargeOnCustomer": '.$ServiceChargeOnCustomer.',
		"Amount": '.$Amount.',
		"Comment": "'.$Comment.'",
		"AmountDeductedFromSupplier": '.$AmountDeductedFromSupplier.'
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
		CURLOPT_URL => "$basURL/v2/MakeRefund",
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
			$RefundId= $json["Data"]["RefundId"];
			$RefundReference= $json["Data"]["RefundReference"];
			if(isset($RefundId) && isset($RefundReference) ){
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