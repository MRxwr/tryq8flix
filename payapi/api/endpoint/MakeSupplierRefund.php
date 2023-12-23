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
		if(isset($_POST['Key'])){ $invoiceid = $_POST['Key']; }else{ 
            $invoiceid ='';
            $validation[$key]['field']='Key';
            $validation[$key]['msg']='Shipping Method is required!';
            $key++;
        }
		if(isset($_POST['KeyType'])){ $KeyType = $_POST['KeyType']; }else{ 
            $ShippingMethod ='';
            $validation[$key]['field']='KeyType';
            $validation[$key]['msg']='Key Type as invoiceid is required!';
            $key++;
        }
		if(isset($_POST['RefundChargeOnCustomer'])){ $RefundChargeOnCustomer = $_POST['RefundChargeOnCustomer']; }else{ 
            $ShippingMethod ='';
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
		if(isset($_POST['VendorDeductAmount'])){ $VendorDeductAmount = $_POST['VendorDeductAmount']; }else{ 
            $VendorDeductAmount ='';
            $validation[$key]['field']='VendorDeductAmount';
            $validation[$key]['msg']='Vendor Deduct Amount is required!';
            $key++;
        }
		if(isset($_POST['Comment'])){ $Comment = $_POST['Comment']; }else{ $Comment =''; }
		if(isset($_POST['Suppliers'])){ $Suppliers = $_POST['Suppliers']; }else{ 
            $Suppliers ='';
            $validation[$key]['field']='Suppliers';
            $validation[$key]['msg']='Suppliers is required!';
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
		"VendorDeductAmount": '.$VendorDeductAmount.',
		"Comment": "'.Comment.'",
		"Suppliers": '.$Suppliers.'
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
		CURLOPT_URL => "$basURL/v2/MakeSupplierRefund",
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