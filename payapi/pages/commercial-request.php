<?php 
if(isset($_POST['submit']))
{
    	$customer_name = $_POST['customer_name'];
			$customer_email = $_POST['customer_email'];
			$business_name = $_POST['business_name'];
			$business_type = $_POST['business_type'];
			$arabic = ['١','٢','٣','٤','٥','٦','٧','٨','٩','٠'];
			$english = [ 1 ,  2 ,  3 ,  4 ,  5 ,  6 ,  7 ,  8 ,  9 , 0];
			$phone = str_replace($arabic, $english, $_POST['mobile_number']);
			$mobile_number = $phone;
			if(isset($_POST['instructions'])){ $instructions = $_POST['instructions']; }else{ $instructions =''; }
					date_default_timezone_set('Asia/Riyadh');
					$created_at = date('Y-m-d H:i:s');
			$customer_mobile=$mobile_number;
	    $tbl_name = 'tbl_booking_commercial';
			$data= "
				customer_name = '$customer_name',
				customer_email = '$customer_email',
				mobile_number = '$mobile_number',
				business_name = '$business_name',
				business_type = '$business_type',
				instructions = '$instructions',
				status ='No',
				created_at = '$created_at'
				";
			$query = $obj->insert_data($tbl_name,$data);
			$res = $obj->execute_query($conn,$query);
			if($res==true){ ?>
        	<div class="alert alert-success">
            <strong><?=$lang['success']?>!</strong> <?=$lang['request_submited']?>
          </div>
			<?php }  
  	  }
					
?>
<style>
        .wrapper{
                  width:100%;
                  padding-top: 20px;
                  text-align:center;
                }
                .slick-prev:hover:before,
                .slick-prev:focus:before,
                .slick-next:hover:before,
                .slick-next:focus:before {
                opacity:1;
                }
                .res_carousel{
                  width:100%;
                  margin:0px auto;
                }
                .slick-slide{
                  margin:10px;
                }
                .slick-slide img{
                  width:100%;
                }
                .slick-prev, .slick-next{
                  background: #fcdfa7;;
                  border-radius: 15px;
                  border-color: transparent;
                  display: block;
                }
                .slick-prev:hover, .slick-next:hover{
                  background: #fdcbec;
                  border-radius: 15px;
                  border-color: transparent;
                }
                .slick-arrow.slick-disabled {
                  display: block !important;
                }
                .card{
                  border: 2px solid #fff;
                  box-shadow: 1px 1px 15px #ccc;
                }
                .card-header{
                  padding:0px;

                }
                .card-body{
                  background: #fff;
                  width: 100%;
                  vertical-align: top;
                }
                .card-content{
                  padding:0px;
                  text-align: left;
                  color: #333;
                  
                }
                .card-title{
                  font-size: 15px;
                  font-weight: 300;
                }
              .res_carousel img {
                  width: 100%;
                }
				.slick-slide img {
					border-radius: 5px;
				}
        </style>
<section>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <!-- <h2 class="shoots-Head2"><?php //echo $lang['business_information'] ?></h2> -->
		  <h2 class="shoots-Head heading_right"><?php echo $lang['price'] ?></h2>
          <div class="row">
              <?php
              $packages=get_packages(17);
              if(!empty($packages)){
              ?>
              <?php
               foreach($packages as $key=>$package){
              $id = $package['id'];
              $price = $package['price'];
              $currency = $package['currency'];
              $post_title = $package['title_'.$_SESSION['lang']];
              $post_description = $package['description_'.$_SESSION['lang']];
              $image_url =$package['image_url'];
              $created_at = $package['created_at'];
              $is_extra = $package['is_extra']; 
              $extra_items = $package['extra_items'];
              $category = $package['category'];
              ?>
                <div class="col-sm-12 col-md-4" style="border: 1px solid #6c757d;">
                  <div style="padding:10px; margin:0 -15px;font-size: 18px;font-weight: bold;"><?=$post_title?></div>
                  <div style="border-top: 1px solid #6c757d;margin:0 -15px;padding:10px">Starting From <?=$price?> KD</div>
                </div>
            <?php } } ?>
          </div>
         
          <div class="row">
              <div class="col-sm-12 col-md-12">
                <!-- Place somewhere in the <body> of your page -->
                <?php $themes = get_works(17);
                   //var_dump($themes->num_rows);
                   if(@$themes->num_rows >0 ){ ?>
                  <h5 class="shoots-Head heading_right"><?php echo $lang['works'] ?></h5>
                  <div class="wrapper" style="direction:ltr;">
                      <div class="work_carousel" style="direction:ltr;">
                        <?php  foreach($themes as $key=>$theme){
                            $title = $theme['title_'.$_SESSION['lang']];
                            $image_url = $theme['image_url'];
                            $themeid = $theme['id'];
                            ?>
                            <div>
                              <div class="card theme_li <?=($key==0?'active':'')?>"  data-id="<?=$themeid?>" data-title="<?=$themeid?>-<?=$title?>">
                                <div class="card-header team-image">
                                <a href="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" data-lightbox="image-<?=$themeid?>" data-title="<?=$title?>">
                                  <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" style="height:140px;" />
                                </a>
                                </div>
                                <!-- <div class="card-body">
                                  <div class="card-content">
                                    <div class="card-title"><?=$title?></div>
                                    <div class="card-text">
                                        <input id="th-<?=$themeid?>" type="radio" name="theme" value="<?=$themeid?>-<?=$title?>" <?=($key==0?'checked':'')?>>
                                    </div>
                                  </div>
                                </div> -->
                              </div>
                            </div> 
                          <?php }   ?> 
                        </div>
                      </div>
                      <?php } ?>
              </div>
          </div>
        </div>
        <div class="col-md-8 col-sm-10">
          <form class="personal-information" method="post" action="">
          <?php
             include('commercial.php');
            ?>
            <div class="row pt-4">
              <div class="col-sm-5 col-md-4">&nbsp;</div>
              <div class="col-sm-7 col-md-8">
                <button type="submit"  name="submit"  class="btn btn-lg btn-outline-primary btn-block btn-rounded"><?php echo $lang['submit'] ?></button>
              </div>  
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  
 