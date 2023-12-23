<?php 
if(isset($_GET['id'])){
  $package = get_packages_details($_GET['id']);
}else{
  $package = get_packages_details(6);
}
         $id = $package['id'];
          $price = $package['price'];
          $currency = $package['currency'];
					$post_title = $package['title_'.$_SESSION['lang']];
          $post_description = $package['description_'.$_SESSION['lang']];
          $image_url =$package['image_url'];
					$created_at = $package['created_at'];
					$is_extra = $package['is_extra']; 
					$extra_items = $package['extra_items'];
          $times = $package['time'];
          
          // Date formate					
          $date = explode('-',$_GET['date']);
          $booking_date = $date[2].'-'.$date[1].'-'.$date[0];				
          $booktimes = get_bookingTimeBydate($_GET['id'],$booking_date);
          $booktimeArr=array(); 
          if(@count($booktimes) != 0){
            foreach($booktimes as $key=>$booktime){		
              $booktimeArr[] = $booktime['booking_time'];
            }
          }
					
?>
<section>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h2 class="shoots-Head2"><?php echo $lang['personal_information'] ?></h2>
        </div>
        <div class="col-md-8 col-sm-10">
          <form class="personal-information" method="post" action="<?php echo SITEURL; ?>payment/process.php">
            
            <input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />
            <input type="hidden" id="booking_price" name="booking_price" value="<?php echo $price; ?>" />
            <input type="hidden" id="hid_extra_items" name="hid_extra_items" value='<?php echo $extra_items; ?>' />
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['package_choosen'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?=$post_title?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['date'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" readonly class="form-control-plaintext" name="booking_date" id="booking_date" value="<?php if(isset($_GET['date'])){echo $_GET['date']; } ?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['preffered_time'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <select class="form-control form-control-lg" id="booking_time" name="booking_time" style="max-width: 300px;" required>
                <option value=""  ><?php echo $lang['select_time'] ?></option>
                <?php $rows = json_decode($times); 
                        foreach($rows as $row ){
							            $time = $row->startDate." - ".$row->endDate;
                            if (!in_array($time, $booktimeArr))
                            {
                            echo "<option value='".$row->startDate." - ".$row->endDate."'>".$row->startDate." - ".$row->endDate."</option> ";
                            } 
                        }
                   ?>
                </select>
              </div>
            </div>
            
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['extra'] ?>:</label>
              <div class="col-sm-7 col-md-8">
               <!-- <label class="form-check-label" for="defaultCheck1">
                	<span class="form-control-plaintext"><?php //echo $lang['filming'] ?></span>
                </label> -->
                
               <?php 
                        $extra_items_rows = json_decode($extra_items); 
						//print_r($extra_items_rows);
						$item = "item_".$_SESSION['lang'];
              foreach($extra_items_rows as $extra_items_row ){
				        ?>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="<?php echo $extra_items_row->$item.",".$extra_items_row->price; ?>" name="select_extra_item[]">
                  <label class="form-check-label" for="defaultCheck1">
                    <span class="form-control-plaintext"><?php echo $extra_items_row->$item." ".$extra_items_row->price." KD."; ?></span>
                  </label>
                </div>
                <?php
				         }
                ?>
                
              </div>
            </div>

            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['customer_name'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" class="form-control form-control-lg" id="customer_name" name="customer_name" required >
              </div>
            </div>

            <div class="form-group row" style="display:none">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['customer_email'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="email" class="form-control form-control-lg" id="customer_email" name="customer_email" value="Hello@myshootskw.com" required>
              </div>
            </div>

            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['mobile_number'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" class="form-control form-control-lg" id="mobile_number" name="mobile_number" required>
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['baby_name'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" class="form-control form-control-lg" id="baby_name" name="baby_name">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['baby_age'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" class="form-control form-control-lg" id="baby_age" name="baby_age">
              </div>
            </div>

            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['instructions'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <textarea name="instructions" id="instructions" class="form-control form-control-lg"  rows="4" placeholder=""></textarea>
              </div>
            </div>

            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"></label>
              <div class="col-sm-12 col-md-8">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" name="termsandcondition" required>
                    <label class="form-check-label" for="defaultCheck1">
                      <span class="form-control-plaintext"> <?php echo $lang['i_agree'] ?> <a href="<?php echo SITEURL; ?>index.php?page=terms-and-condition" targer="_blank"><?php echo $lang['terms_and_condition'] ?></a> </span>
                    </label>
                  </div>
				  
				  <div class="reservation">
          <h5 class="theme-color mt-4" style="color:red;">
            <span>Deposit:</span> <span>30.500 KD</span>
          </h5>
          <p class="theme-color mb-1 pl-2" style="color:red;">
            Deposit are not refundable.
          </p>
          <p class="theme-color pl-2" style="color:red;">
            0.500 is the payment gateway transaction fees.
          </p>
        </div> 
              </div>
            </div>
			
			<div class="row pt-4">
              <div class="col-sm-5 col-md-4">&nbsp;</div>
              <div class="col-sm-7 col-md-8">
                <button type="submit"  name="submit"  class="btn btn-lg btn-outline-primary btn-block btn-rounded"><?php echo $lang['continue_to_payment'] ?></button>
              </div>  
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  
 