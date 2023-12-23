
  <section class="packages_new">
    <div class="container">
      <div class="row">
      <?php 
        if(isset($_GET['cat'])){
          $packages=get_packages($_GET['cat']);
          $cat1 =  get_category_details($_GET['cat']);
          $cate_title = $cat1['title_'.$_SESSION['lang']];
          }else{
          $cate_title =$lang['our_packages'];
          $packages=get_packages();
        }
      ?>
        <div class="col-12" align="center">
          <h2 class="shoots-Head" style="display:inline-block;"><?php echo  $cate_title; ?></h2>
        </div>
      </div>
      <div class="row">
      <?php foreach($packages as $key=>$package){
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
        <!-- Package Div Start -->
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <div class="packages_box">
            <div class="package_border">
            <div class="package_box_img">
            <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" class="img-rounded d-block mx-auto mb-md-0 mb-3" style="max-width:100%;">
            </div>
                            <h3><?=$post_title?></h3>
                             <div style="height: 250px;padding-right: 10px;">
                               <p><?=$post_description?></p>
                              </div>
                            <h5><span><?php echo $lang['PriceText'] ?>: </span><span class="ml-2"><?=$price?> <?=$currency?></span></h5>
                            </div>
                            <a href="<?php echo SITEURL; ?>index.php?page=reservations&id=<?=$id?>&cid=<?=$category?>">Book Now</a>
                        </div>
              <!-- <a href="<?php echo SITEURL; ?>index.php?page=reservations&id=<?=$id?>">
              <div class="package-card card m-2">
                <div class="card-body p-2">
                  <div class="row align-items-center no-gutters">
                  
                    <div class="col-lg-7 col-md-8 col-sm-12 order-lg-1 order-md-1 order-sm-2 order-2">
                    <h5 class="package-head"><?=$post_title?></h5>
                      <?=$post_description?>
                       <?php if($is_extra == 1) { ?>
                      <h5><?php //echo $lang['extra_charges'] ?></h5>
                      <ul class="list-unstyled">
						             <?php 
                        // $rows = json_decode($extra_items); 
                        // foreach($rows as $row ){
                        // echo "<li>- ".$row->item." ".$row->price." KD.</li>";
                        //}
                        ?>
                      </ul>
                      <?php } ?> 
                      <p class="theme-color package-price-tag text-right"><span>Price:</span><span class="ml-2"><?=$price?> <?=$currency?></span></p>
                    </div>
                    <div class="col-lg-5 col-md-4 order-lg-3 order-md-2 order-sm-1 order-1">
                      <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" class="img-rounded img-fluid d-block mx-auto mb-md-0 mb-3">
                    </div>
                  </div>
                </div>
              </div>
              </a> -->
            </div>
        <!-- Package Div End -->
  <?php } ?>
      </div>
    </div>
  </section>
  <!--Package End-->
  
<!--   
  <div class="packages_new">
  <div class="container">
        	<div class="row">
                    
                
                    <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                        <div class="packages_box">
                            <img src="<?php echo SITEURL; ?>assets/images/package_img1.jpg" width="100%">
                            <h3>Family  Package</h3>
                            <p>Includes parents with their kids.</p>
                            <p>Includes parents with their kids.</p>
                            <p>Includes parents with their kids.</p>
                            <h5>Price: 80 KD</h5>
                            <a href="">Book Now</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                    	<div class="packages_box">
                            <img src="<?php echo SITEURL; ?>assets/images/package_img2.jpg" width="100%">
                            <h3>Family  Package</h3>
                            <p>Includes parents with their kids.</p>
                            <p>Includes parents with their kids.</p>
                            <p>Includes parents with their kids.</p>
                            <h5>Price: 80 KD</h5>
                            <a href="">Book Now</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                    	<div class="packages_box">
                            <img src="<?php echo SITEURL; ?>assets/images/package_img3.jpg" width="100%">
                            <h3>Family  Package</h3>
                            <p>Includes parents with their kids.</p>
                            <p>Includes parents with their kids.</p>
                            <p>Includes parents with their kids.</p>
                            <h5>Price: 80 KD</h5>
                            <a href="">Book Now</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                    	<div class="packages_box">
                            <img src="<?php echo SITEURL; ?>assets/images/package_img4.jpg" width="100%">
                            <h3>Family  Package</h3>
                            <p>Includes parents with their kids.</p>
                            <p>Includes parents with their kids.</p>
                            <p>Includes parents with their kids.</p>
                            <h5>Price: 80 KD</h5>
                            <a href="">Book Now</a>
                        </div>
                    </div>
           		 </div>
            </div>
        </div> -->
        
        
<!--  <section class="pb-0">
    <div class="container" style="max-width: 1340px;">
      <div class="row">
        <div class="col-12">
          <h2 class="shoots-Head"><?php echo $lang['follow_us'] ?></h2>
        </div>
      </div>
    </div>
  </section>
  <iframe name="frame" style="width:100%; min-height:350px;" id="frame" src="<?php echo SITEURL; ?>pages/insta.php" allowtransparency="true" frameborder="0"></iframe>

  <section class="pb-0">
    <div class="container" style="max-width: 1340px;">
      <div class="row">
        <div class="col-12">
          <h2 class="shoots-Head"><?php echo $lang['about_us'] ?></h2>
        </div>
      </div>
    </div>
    <div class="container-fluid p-0 bg-light">
      <div class="row no-gutters align-items-center">
        <div class="col-md-7">
          <img src="<?php echo SITEURL; ?>assets/img/shoots-about.png" class="img-fluid d-block mx-auto">
        </div>
        <div class="col-md-5 p-3 p-md-5">
          <h2 class="mb-5">Photography</h2>
          <h5 class="mb-4">Creative Photography Theme</h5>
                <?php 
               $about=get_page_details(7);
               ?>
          <p class="about-para"><?php echo $about['description_'.$_SESSION['lang']]; ?></p>
          <a href="<?php echo SITEURL; ?>index.php?page=galleries" class="btn btn-lg btn-outline-secondary px-5 mt-5">Gallery</a>
        </div>
      </div>
    </div>
  </section>-->