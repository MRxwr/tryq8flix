<?php 
if(isset($_GET['id'])){
  $package = get_packages_details($_GET['id']);
}else{
  $package = get_packages_details(6);
}
          $cid = $_GET['cid'];
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
            <input type="hidden" id="plebel" name="plebel" value='' />
            <input type="hidden" id="lebel_price" name="lebel_price" value='' />
            <input type="hidden" id="theme" name="theme" value='' />
            <input type="hidden" id="discount_price" name="discount_price" value='' />
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['package_choosen'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?=$post_title?>">
              </div>
            </div>
            <div class="form-group row">
            <?php 
              $disable_times = [];
              if(isset($_GET['date'])){ 
                $bdt = explode('-',$_GET['date']);
                $booking_date = $bdt[1].'/'.$bdt[0].'/'.$bdt[2]; 
                $bkdate = $bdt[2].'/'.$bdt[1].'/'.$bdt[0];
                $dtimes = getDisableTime($booking_date);
                $disable_times = json_decode($dtimes);
              }
             
            ?>
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['date'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" readonly class="form-control-plaintext" name="booking_date" id="booking_date" value="<?php if(isset($_GET['date'])){echo $_GET['date']; } ?>">
              </div>
            </div>
            <?php
            
             include('general.php');
            ?>

            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"></label>
              <div class="col-sm-12 col-md-8">
                  <!-- <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" checked name="termsandcondition" required>
                    <label class="form-check-label" for="defaultCheck1">
                      <span class="form-control-plaintext"> <?php echo $lang['i_agree'] ?> <a href="#" data-toggle="modal" data-target="#myModal"><?php echo $lang['terms_and_condition'] ?></a> </span>
                    </label>
                  </div> -->
                    <div class="reservation">
                    <h5 class="total" style="text-align:center!important; font-size:16px; color:red;">
                      <span><?php echo $lang['Deposit'] ?>:</span>
                      <span id="totalprice"></span>
                      <span><?php echo $lang['Kd'] ?></span>
                      <span id="newprice"></span>
                      <span id="discountprice"></span>
                      
                    </h5>
                    <h5 class="theme-color mt-4" style="color:red;">
                      <span><?php echo $lang['Deposit'] ?>:</span> <span><?php echo $lang['30Kd'] ?></span>
                    </h5>
                  
                    <p class="theme-color mb-1 pl-2" style="color:red;">
                      <?php echo $lang['DepositNotRefundable'] ?>
                    </p>
                    <!--<p class="theme-color pl-2" style="color:red;">-->
                    <!--  0.500 is the payment gateway transaction fees.-->
                    <!--</p>-->
                    
                  </div> 
              </div>
            </div>

            <!-- <div class="row">
              <div class="col-sm-2 col-md-1">&nbsp;</div>
              <div class="col-sm-9 col-md-10">
                <div class="row pt-4">
                <div class="col-sm-12 col-md-12">
                  <div class="form-group row">
                    <label for="" class="col-sm-4 col-md-4 col-form-label">  <?php echo $lang['have_coupon'] ?>:</label>
                    <div class="col-sm-5 col-md-5">
                      <input type="text" style="margin-top: 10px" class="form-control form-control-lg" id="coupon_code" name="coupon_code">
                    </div>
                    <div class="col-sm-3 col-md-3">
                      <input type="button" style="width: 100%;margin-top: 0.5rem;padding:8px;" class="btn btn-lg btn-outline-primary btn-block btn-rounded" name="apply" id="coupon_appy" value="<?php echo $lang['apply'] ?>">
                    </div>
                  </div>
                  </div>

                 </div>
              </div>
              <div class="col-sm-2 col-md-1">&nbsp;</div>
            </div> -->

            <div class="row">
              <div class="col-sm-2 col-md-1">&nbsp;</div>
              <div class="col-sm-9 col-md-10">
                <div class="row pt-4">
                <div class="col-sm-12 col-md-12">
                  <div class="form-group row">
                    <label for="" class="col-sm-4 col-md-4 col-form-label">  <?php echo $lang['referral_code'] ?>:</label>
                    <div class="col-sm-5 col-md-5">
                      <input type="text" style="margin-top: 10px" class="form-control form-control-lg" id="referral_code" name="referral_code">
                    </div>
                    <div class="col-sm-3 col-md-3">
                      <input type="button" style="width: 100%;margin-top: 0.5rem;padding:8px;" class="btn btn-lg btn-outline-primary btn-block btn-rounded" name="apply" id="referral_appy" value="<?php echo $lang['apply'] ?>">
                    </div>
                  </div>
                  </div>

                 </div>
              </div>
              <div class="col-sm-2 col-md-1">&nbsp;</div>
            </div>
			      
            <div class="row pt-4">
              <div class="col-sm-5 col-md-4">&nbsp;</div>
              <div class="col-sm-7 col-md-8">
                <a  class="btn btn-lg btn-outline-primary btn-block btn-rounded" data-toggle="modal" data-target="#myModal" ><?php echo $lang['continue_to_payment'] ?></a>
               <!-- Modal -->
            <div id="myModal" class="modal fade" role="dialog">
              <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" style="width: auto;" data-dismiss="modal">&times;</button>
                     <div style="text-align:center;width: 100%;">
                         <h4 class="modal-title" style="text-align:center;direction: unset;margin:0px auto;"><?php echo $lang['terms_and_condition'] ?></h4></div> 
                    </div>
                    <div class="modal-body">
                        <?php 
                          $tearm=get_page_details(9);
                            echo $tearm['description_'.$_SESSION['lang']]
                          ?>
                    </div>
                      <div class="modal-footer" style="text-align:center;">
                        
                        <button type="submit"  name="submit" style="width: 50%;margin: 0px auto;"  class="btn btn-lg btn-outline-primary btn-block btn-rounded"><?php //echo $lang['i_agree'] ?> <?php echo $lang['continue_to_payment'] ?></button>
                      </div>
                      </div>
                     </div>
                   </div>
                  </div>
                </div>
              </div>  
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
 
    