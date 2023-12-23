
	  


  <section>
    <div class="container">
      <div class="row">
        <div class="col-12 text-center">
            <!-- <button class="btn btn-sm theme-bg text-uppercase text-white" onclick="one()">Single Grid</button> -->
            <button class="btn btn-sm theme-bg text-uppercase text-white" onclick="two()">Double Grid</button>
            <button class="btn btn-sm theme-bg text-uppercase text-white active" onclick="four()">4 Grids</button>
        </div>
        <div class="col-lg-8 col-md-10 mx-auto">
          <div class="mt-3 gallery-grid">          
                  <?php 
				  $cal= 0;
				  $cal_end= 3;
				  $i= 0;

  $galleries=get_galleries();
  foreach($galleries as $key=>$gallery){
	  $title = $gallery['title_'.$_SESSION['lang']];
	  $image_url = $gallery['image_url'];

	  if($i == $cal){
		  echo '<div class="column">';
		  $cal = $cal+4;
	  }
		   
	  ?>
              <a class="example-image-link" href="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" data-lightbox="example-set" data-title="<?php echo $title; ?>">
                <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" style="width:100%">
              </a>
              <?php
			 if($i == $cal_end){
		  echo '</div>';
		   $cal_end = $cal_end+4;
	       }
	  $i= $i+1;
			    }
	  if($i % 4 != 0){
		   echo '</div>';
	  }
	  ?>
     
<!--
            <div class="column">
              <a class="example-image-link" href="<?php echo SITEURL; ?>assets/img/gallery/img-2.jpg" data-lightbox="example-set" data-title="img Title">
                <img src="<?php echo SITEURL; ?>assets/img/gallery/img-2.jpg" style="width:100%">
              </a>
              <a class="example-image-link" href="<?php echo SITEURL; ?>assets/img/gallery/img-3.jpg" data-lightbox="example-set" data-title="img Title">
                <img src="<?php echo SITEURL; ?>assets/img/gallery/img-3.jpg" style="width:100%">
              </a>
              <a class="example-image-link" href="<?php echo SITEURL; ?>assets/img/gallery/img-8.jpg" data-lightbox="example-set" data-title="img Title">
                <img src="<?php echo SITEURL; ?>assets/img/gallery/img-8.jpg" style="width:100%">
              </a>
              <a class="example-image-link" href="<?php echo SITEURL; ?>assets/img/gallery/img-9.jpg" data-lightbox="example-set" data-title="img Title">
                <img src="<?php echo SITEURL; ?>assets/img/gallery/img-9.jpg" style="width:100%">
              </a>
            </div>
            <div class="column">
              <a class="example-image-link" href="<?php echo SITEURL; ?>assets/img/gallery/img-4.jpg" data-lightbox="example-set" data-title="img Title">
                <img src="<?php echo SITEURL; ?>assets/img/gallery/img-4.jpg" style="width:100%">
              </a>
              <a class="example-image-link" href="<?php echo SITEURL; ?>assets/img/gallery/img-10.jpg" data-lightbox="example-set" data-title="img Title">
                <img src="<?php echo SITEURL; ?>assets/img/gallery/img-10.jpg" style="width:100%">
              </a>
               <a class="example-image-link" href="<?php echo SITEURL; ?>assets/img/gallery/img-5.jpg" data-lightbox="example-set" data-title="img Title">
                <img src="<?php echo SITEURL; ?>assets/img/gallery/img-5.jpg" style="width:100%">
              </a>
              <a class="example-image-link" href="<?php echo SITEURL; ?>assets/img/gallery/img-6.jpg" data-lightbox="example-set" data-title="img Title">
                <img src="<?php echo SITEURL; ?>assets/img/gallery/img-6.jpg" style="width:100%">
              </a>
            </div>
            <div class="column">
              <a class="example-image-link" href="<?php echo SITEURL; ?>assets/img/gallery/img-5.jpg" data-lightbox="example-set" data-title="img Title">
                <img src="<?php echo SITEURL; ?>assets/img/gallery/img-5.jpg" style="width:100%">
              </a>
              <a class="example-image-link" href="<?php echo SITEURL; ?>assets/img/gallery/img-6.jpg" data-lightbox="example-set" data-title="img Title">
                <img src="<?php echo SITEURL; ?>assets/img/gallery/img-6.jpg" style="width:100%">
              </a>
              <a class="example-image-link" href="<?php echo SITEURL; ?>assets/img/gallery/img-11.jpg" data-lightbox="example-set" data-title="img Title">
                <img src="<?php echo SITEURL; ?>assets/img/gallery/img-11.jpg" style="width:100%">
              </a>
              <a class="example-image-link" href="<?php echo SITEURL; ?>assets/img/gallery/img-12.jpg" data-lightbox="example-set" data-title="img Title">
                <img src="<?php echo SITEURL; ?>assets/img/gallery/img-12.jpg" style="width:100%">
              </a>
            </div>-->
          </div>
        </div>   
      </div>
    </div>  
  </section>