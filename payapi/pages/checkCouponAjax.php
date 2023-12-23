<?php 
	include('../languages/lang_config.php');
	include('../admin/config/apply.php');
    include('../includes/functions.php');
if(isset($_POST['coupon_code']) && isset( $_POST['total_price'])){
     $total_price = $_POST["total_price"];
     $coupon_code = $_POST["coupon_code"];
     $coupon = get_coupon_details($coupon_code);
    if(!empty($coupon)){
        //var_dump($coupon['coupon_type']);
        if($coupon['coupon_type']==1){
            $discount = $total_price*$coupon['coupon_discount']/100;
        }else{
            $discount = $coupon['coupon_discount'];
        }

        $data['status'] = 'success';
        $data['msg'] = 'Successfully applied';
        $data['discount'] = $discount;
    }else{
        $data['status'] = 'error';
        $data['msg'] = 'Invalid Coupon Code Or Expire the code';
        $data['discount'] = 0;
    }
   echo  json_encode($data);
    
}		
?>
