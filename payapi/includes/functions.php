<?php 
  function get_setting($set){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_settings';
	$where = " id=1 ";
	$query = $obj->select_data($tbl_name,$where);
	$res = $obj->execute_query($conn,$query);
	if($res == true)
	{
		$count_rows = $obj->num_rows($res);
		if($count_rows>0){
			$data = mysqli_fetch_array($res);
			if(isset($data[$set])){
				return $data[$set];
				
			}else{
				return false;
			}
				
		}
		 
	}
  }
    // get  package details by package id
  function check_get_apikey($apikey){
	GLOBAL $obj,$conn;
	$tbl_name = 'tbl_apikeys';
	$where = "is_active='Yes' && api_key='".$apikey."'";
	$query = $obj->select_data($tbl_name,$where);
	$res = $obj->execute_query($conn,$query);
	if($res == true)
	{
	  $count_rows = $obj->num_rows($res);
	  if($count_rows>0){
		//return $res -> fetch_row();
		return mysqli_fetch_array($res);
			  
	  }
	   
  }
}

function addApiHistories($post,$data){
    	GLOBAL $obj,$conn;
	if(!empty($post) && !empty($data) ){
		$endpoint = $post['endpoint'];
		$apikey = $post['apikey'];
		$status = $data['type'];
		$msg = $data['msg'];
		$response = json_encode($data);
		date_default_timezone_set("Asia/Riyadh");
		$created_at = date('Y-m-d H:i:s');
		$tbl_name = 'tbl_apihistories';
			$datas= "
				api_key = '$apikey',
				endpoint = '$endpoint',
				status = '$status',
				msg  = '$msg',
				response  = '$response',
				created_at = '$created_at'
			";
			$query = $obj->insert_data($tbl_name,$datas);
			$res = $obj->execute_query($conn,$query);
			$histId=mysqli_insert_id($conn);
			//var_dump(mysqli_insert_id($conn));
			if($histId && $status =='success'){
			    $tbl_nameh = 'tbl_orderIds';
			    $InvoiceId=$data['data']['InvoiceId'];
    			$hdatas= "
    				invoiceId = '$InvoiceId',
    				histId  = '$histId'
    			    ";
    			$query2 = $obj->insert_data($tbl_nameh,$hdatas);
    			$res = $obj->execute_query($conn,$query2);
			}

	}

}

function getInvoiceId($digits){
    GLOBAL $obj,$conn;
    $random_number=''; // set up a blank string
    $count=0;
    while ( $count < $digits ) {
        $random_digit = mt_rand(0, 9);
        $random_number .= $random_digit;
        $count++;
    }
    $tbl_name = 'tbl_orderIds';
	$where = "invoiceId='".$random_number."'";
	$query = $obj->select_data($tbl_name,$where);
	$res = $obj->execute_query($conn,$query);
	if($res == true)
	{
	  $count_rows = $obj->num_rows($res);
	  if($count_rows>0){
	    getInvoiceId($digits);
	  }else{
	      return $random_number;
	  }
	   
    }
  
}


?>

