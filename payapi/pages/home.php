
  <section class="pb-0" style="padding-top: 72px !important;">
    <div class="container" style="max-width: 1340px;">
      <div class="row">
        <div class="col-12">
          
        </div>
      </div>
    </div>
    <div class="container-fluid p-0">
      <div class="row no-gutters align-items-center">
        <div class="col-md-7">
         <!-- <img src="<?php echo SITEURL; ?>assets/img/shoots-about.png" class="img-fluid d-block mx-auto">-->
          <div class="about_l">
          		<img src="<?php echo SITEURL; ?>assets/images/girl_img.png?v=1" class="img-fluid d-block mx-auto">
           </div>
        </div>
        <div class="col-md-5 p-3 p-md-5">
        	<h2 class="shoots-Head home_about"><?php echo $lang['about_us'] ?></h2>
         <!-- <h2 class="mb-5">Photography</h2>
          <h5 class="mb-4">Creative Photography Theme</h5>-->
                <?php 
               $about=get_page_details(7);
               ?>
          <p class="about-para"><?php echo $about['description_'.$_SESSION['lang']]; ?></p>
          <a href="<?php echo SITEURL; ?>index.php?page=galleries" class="btn btn-lg btn-outline-secondary px-5 mt-5">Gallery</a>
        </div>
      </div>
    </div>
  </section>
  
  <section class="homebooknow">
   <div class="container" style="max-width: 1340px;">
     <div class="row">
        <div class="col-12">
          <h2 class="shoots-Head textcenterheading"><?php echo $lang['our_packages'] ?></h2>
        </div>
      </div>
  </div>
        <ul class="desktopbox">
            <?php $cat1 =  get_category_details(12);
            $image_1sturl = $cat1['image_url'];
            $post_1title = $cat1['title_'.$_SESSION['lang']];
            ?>
        	<!--<li> <a href="#"><img src="<?php echo SITEURL; ?>assets/images/booknow_img1.jpg"></a></li>-->
        		<div style="border: 5px solid #fdcbec;border-radius: 10px;margin: 50px;">
        		<li> <a href="#"><img src="<?php echo SITEURL; ?>uploads/images/<?=$image_1sturl?>"></a></li>
            <li>
            	<div class="booknowbox">
                    <!-- <h3 style="padding: 12px 0 10px 0;"><?php //echo $post_1title; ?></h3> -->
                    <h3 style="padding: 5px 0 10px 0;"><?php echo $lang['Anniversary photography'] ?></h3>
                    <h3 style="padding: 12px 0 10px 0;"> <?php echo $lang['without theme'] ?></h3>
                    <a href="<?php echo SITEURL; ?>index.php?page=packages&cat=12"><?php echo $lang['BookNow'] ?></a>
                    <div align="center"><img src="<?php echo SITEURL; ?>assets/images/booknow_border.png"/></div>
                    <h2>P H O T O G R A P H Y</h2>
                    <h5>CAPTURE THE MOMENT OF JOY</h5>
                </div>
            
            </li>
            </div>
            <div style="border: 5px solid #fdefd3;border-radius: 10px;/* padding: 50px; */margin: 50px;">
            <?php $cat2 =  get_category_details(13);
            $image_2ndurl = $cat2['image_url'];
            $post_2title = $cat2['title_'.$_SESSION['lang']];
            ?>
            <li>
            	<div class="booknowbox">
                    <!-- <h3 style="padding: 12px 0 10px 0;"><?php echo $post_2title?></h3> -->
                    <h3 style="padding: 5px 0 10px 0;"> <?php echo $lang['Events photography'] ?></h3>
                    <h3 style="padding: 12px 0 10px 0;"><?php echo $lang['with theme'] ?></h3> 
                   
                    <a href="<?php echo SITEURL; ?>index.php?page=packages&cat=13"><?php echo $lang['BookNow'] ?></a>
                    <div align="center"><img src="<?php echo SITEURL; ?>assets/images/booknow_border.png"/></div>
                    <h2>P H O T O G R A P H Y</h2>
                    <h5>CAPTURE THE MOMENT OF JOY</h5>
                </div>
            </li>
            
            <!--<li> <a href="#"><img src="<?php echo SITEURL; ?>assets/images/booknow_img3.jpg"></a></li>-->
            <li> <a href="#"><img src="<?php echo SITEURL; ?>uploads/images/<?=$image_2ndurl?>"></a></li>
            </div>
            
            <?php $cat3 =  get_category_details(17);
            //var_dump($cat3 );
            $image_3sturl = $cat3['image_url'];
            $post_3title = $cat3['title_'.$_SESSION['lang']];
            ?>
        	<!--<li> <a href="#"><img src="<?php echo SITEURL; ?>assets/images/booknow_img1.jpg"></a></li>-->
        		<div style="border: 5px solid #fdcbec;border-radius: 10px;margin: 50px;">
        		<li> <a href="#"><img src="<?php echo SITEURL; ?>uploads/images/<?=$image_3sturl?>"></a></li>
            <li>
            	<div class="booknowbox">
                    <h3><?php echo $post_3title; ?></h3>
                    <a href="<?php echo SITEURL; ?>index.php?page=commercial-request"><?php echo $lang['BookNow'] ?></a>
                    <div align="center"><img src="<?php echo SITEURL; ?>assets/images/booknow_border.png"/></div>
                    <h2>P H O T O G R A P H Y</h2>
                    <h5>CAPTURE THE MOMENT OF JOY</h5>
                </div>
            
            </li>
            </div>
        </ul>
        
        <ul class="mobilebox">
        	<!--<li> <a href="#"><img src="<?php echo SITEURL; ?>assets/images/booknow_img1.jpg"></a></li>-->
        	<div style="
    border: 5px solid #fdcbec;
    border-radius: 10px;
    margin: 10px;
    padding-top: 24px;
">
        	<li> <a href="#"><img src="<?php echo SITEURL; ?>uploads/images/<?=$image_1sturl?>"></a></li>
            <li>
            	<div class="booknowbox">
                    <!-- <h2 class="mb-3" style="padding: 12px 0 10px 0;"><?php //echo $post_1title ?></h2> -->
                    <h3 style="padding: 8px 0 8px 0;font-size: 14px;"><?php echo $lang['Anniversary photography'] ?></h3>
                    <h3 style="padding: 4px 0 8px 0;font-size: 14px;"><?php echo $lang['without theme'] ?></h3>
                    <a style="padding: 6px 38px !important;font-size: 12px;" href="<?php echo SITEURL; ?>index.php?page=packages&cat=12"><?php echo $lang['BookNow'] ?></a>
                    <div align="center"><img src="<?php echo SITEURL; ?>assets/images/booknow_border.png"/></div>
                    <h2 style="font-size: 14px;">P H O T O G R A P H Y</h2>
                    <h5 style="padding: 6px 0 0 0;font-size: 12px;">CAPTURE THE MOMENT OF JOY</h5>
                </div>
            
            </li>
            </div>
            
             <!--<li> <a href="#"><img src="<?php echo SITEURL; ?>assets/images/booknow_img3.jpg"></a></li>-->
             <div style="border: 5px solid #fdefd3;border-radius: 10px;margin: 10px;padding-top: 24px;">
             <li> <a href="#"><img src="<?php echo SITEURL; ?>uploads/images/<?=$image_2ndurl?>"></a></li>
            <li>
            	<div class="booknowbox">
                	<!-- <h2 class="mb-3" style="padding: 12px 0 10px 0;"><?php //echo $post_2title ?></h2> -->
                  <h3 style="padding: 8px 0 8px 0;font-size: 14px;"><?php echo $lang['Events photography'] ?></h3>
                  <h3 style="padding: 4px 0 8px 0;font-size: 14px;"><?php echo $lang['with theme'] ?></h3>
                    <a style="padding: 6px 38px !important;font-size: 12px;" href="<?php echo SITEURL; ?>index.php?page=packages&cat=13"><?php echo $lang['BookNow'] ?></a>
                    <div align="center"><img src="<?php echo SITEURL; ?>assets/images/booknow_border.png"/></div>
                    <h2 style="font-size: 14px;">P H O T O G R A P H Y</h2>
                    <h5 style="padding: 6px 0 0 0;font-size: 12px;">CAPTURE THE MOMENT OF JOY</h5>
                </div>
            </li>
            </div>
            <div style="border: 5px solid #fdefd3;border-radius: 10px;margin: 10px;padding-top: 24px;">
             <li> <a href="#"><img src="<?php echo SITEURL; ?>uploads/images/<?=$image_3ndurl?>"></a></li>
            <li>
            	<div class="booknowbox">
                	<h2 class="mb-3" style="padding: 8px 0 8px 0;font-size: 14px;"><?php echo $post_3title ?></h2>
                    <a style="padding: 6px 38px !important;font-size: 12px;" href="<?php echo SITEURL; ?>index.php?page=commercial-request&cat=17"><?php echo $lang['BookNow'] ?></a>
                    <div align="center"><img src="<?php echo SITEURL; ?>assets/images/booknow_border.png"/></div>
                    <h2 style="font-size: 14px;">P H O T O G R A P H Y</h2>
                    <h5 style="padding: 6px 0 0 0;font-size: 12px;">CAPTURE THE MOMENT OF JOY</h5>
                </div>
            </li>
            </div>
           
        </ul>
  
  </section>
  
  
  <section class="d-none">
    <div class="container" style="max-width: 1340px;">
      <div class="row">
        <div class="col-12">
          <h2 class="shoots-Head"><?php echo $lang['our_packages'] ?></h2>
        </div>
      </div>
      <div class="row no-gutters">
      <?php 
  $packages=get_categories();
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
     ?>
        <!-- Package Div Start -->
           <div class="col-md-6 col-sm-6 col-12">
              <a href="<?php echo SITEURL; ?>index.php?page=reservations&id=<?=$id?>">
              <div class="package-card card m-2">
                <div class="card-body p-2">
                  <div class="row align-items-center no-gutters">
                  
                    <div class="col-lg-7 col-md-8 col-sm-12 order-lg-1 order-md-1 order-sm-2 order-2">
                    <h5 class="package-head"><?=$post_title?></h5>
                      <?=$post_description?>
                      <!-- <?php if($is_extra == 1) { ?>
                      <h5><?php //echo $lang['extra_charges'] ?></h5>
                      <ul class="list-unstyled">
						             <?php 
                        // $rows = json_decode($extra_items); 
                        // foreach($rows as $row ){
                        // echo "<li>- ".$row->item." ".$row->price." KD.</li>";
                        //}
                        ?>
                      </ul>
                      <?php } ?> -->
                      <p class="theme-color package-price-tag text-right"><span>Price:</span><span class="ml-2"><?=$price?> <?=$currency?></span></p>
                    </div>
                    <div class="col-lg-5 col-md-4 order-lg-3 order-md-2 order-sm-1 order-1">
                      <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" class="img-rounded img-fluid d-block mx-auto mb-md-0 mb-3">
                    </div>
                  </div>
                </div>
              </div>
              </a>
            </div>
        <!-- Package Div End -->
  <?php } ?>
        
      </div>
    </div>
  </section>
  <!--Package End-->
  <section class="pb-0">
    <div class="container" style="max-width: 1340px;">
      <div class="row">
        <div class="col-12">
          <h2 class="shoots-Head textcenterheading"><?php echo $lang['follow_us'] ?></h2>
        </div>
      </div>
    </div>
  </section>
  <iframe name="frame" style="width:100%; min-height:390px;" id="frame" src="<?php echo SITEURL; ?>pages/insta.php" allowtransparency="true" frameborder="0"></iframe>

 <!-- The Modal -->
 <?php if(get_setting('is_modal')==1){
    $title = get_setting('ads_title_'.$_SESSION['lang']);
    ?>
  <div id="homeImageModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal" style="position:fixed; top:20px; padding:5px;">&times;</button>
      <div class="modal-header">
        <div style="text-align:center; margin: 0 auto;"><h4><?=$title?></h4></div>
       
      </div>
      <div class="modal-body">
        <!-- <a href="<?=get_setting('modal_url')?>">
          <img class="modal-content" src="<?=SITEURL?>uploads/images/<?=get_setting('modal_img')?>" id="img01">
        </a> -->
        <?php $ads=get_ads(); 
                  if(!empty($ads)){ ?>  
                  <div id="slider1" class="flexslider" style="orverflow:hidden">
                   <ul class="slides" style="margin: 0px -20px;">
                      <?php foreach($ads as $key=>$gallery){
                          $title = $gallery['title_'.$_SESSION['lang']];
                          $image_url = $gallery['image_url'];
                          ?>
                           <li>
                           
                               <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" >
                           </li>
                        <?php } ?>
                      </ul>
                  </div>
            <?php } ?>      
        <div style="text-align:center; margin:15px;">
            <a style="background-color: #fdcbec;color: #fff;border-radius: 10px;padding:10px 25px;" href="<?=get_setting('modal_url')?>"><?php echo $lang['BookNow'] ?></a>
        </div>
      </div>
      
    </div>

  </div>
</div>
  
<?php	} ?>
