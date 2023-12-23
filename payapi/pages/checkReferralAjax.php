<?php 
	include('../languages/lang_config.php');
	include('../admin/config/apply.php');
    include('../includes/functions.php');
if(isset($_POST['referral_code'])){
     $referral_code = $_POST["referral_code"];
     $referral = get_referral_details($referral_code);
    if(!empty($referral)){
        $data['status'] = 'success';
        $data['msg'] = 'Successfully applied';
        $data['code'] = $referral_code;
    }else{
        $data['status'] = 'error';
        $data['msg'] = 'Invalid referral Code Or Expire the code';
        $data['code'] = 0;
    }
   echo  json_encode($data);
    
}		
?>
