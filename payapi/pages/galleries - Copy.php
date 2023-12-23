
	  


  <section class="gallery_panel">
    <div class="container">
      <div class="row">
      <?php 
		
		if(isset($_GET['id'])){ ?>

      <div class="col-12 text-center">
            <!-- <button class="btn btn-sm theme-bg text-uppercase text-white" onclick="one()">Single Grid</button> -->
            <button class="btn btn-sm theme-bg text-uppercase text-white" onclick="two()">Double Grid</button>
            <button class="btn btn-sm theme-bg text-uppercase text-white active" onclick="four()">4 Grids</button>
        </div>
        <div class="col-lg-8 col-md-10 mx-auto">
          <div class="mt-3 gallery-grid">          
                  <?php $galleries=get_galleries($_GET['id']); 
                  if(!empty($galleries)){ ?>
                     <div class="column"><?php generateGalleryCols(1,$galleries) ?></div>
                     <div class="column"><?php generateGalleryCols(2,$galleries) ?></div>
                     <div class="column"><?php generateGalleryCols(3,$galleries) ?></div>
                     <div class="column"><?php generateGalleryCols(4,$galleries) ?></div>
                     <?php } ?>
          </div>
        </div>

    <?php }else{ ?>

         <div class="col-12 text-center">
           
        </div>
        <div class="col-lg-10 col-md-10 mx-auto">
          <div class="row">          
                 <?php 
                    $tbl_name = 'tbl_album';
                    $where = "is_active='Yes'";
                    $query = $obj->select_data($tbl_name,$where);
                      $res = $obj->execute_query($conn,$query);
                      while ($row=$obj->fetch_data($res)) {
                        $id = $row['id'];
                        $title = $row['title_'.$_SESSION['lang']]; 
                        $image_url = $row['image_url'];
                      ?>
                        <div class="col-4 text-center">
                        <a class="example-image-link" href="<?php echo SITEURL; ?>index.php?page=galleries&id=<?=$id?>">
                          <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" style="width:100%">
                          <div class="album_title">
                          <h3><?=$title?></h3></div>
                        </a>
                           <a href="<?php echo SITEURL; ?>index.php?page=categories" class="btn btn-lg btn-outline-secondary px-5 mt-5"><?php echo $lang['BookNow'] ?></a>
                        </div>
                      <?php  } ?>
                    </div>
                  </div>

    <?php } ?>
           
      </div>
    </div>  
  </section>
  <?php
  function generateGalleryCols($cols,$galleries){
    $cols1=array(0,4,12,16,20,24);
    $cols2=array(1,5,8,9,13,17,21,25);
    $cols3=array(2,6,10,14,18,22,26);
    $cols4=array(3,7,11,16,19,23,27);
    foreach($galleries as $key=>$gallery){
      $title = $gallery['title_'.$_SESSION['lang']];
      $image_url = $gallery['image_url'];
      if($cols == 1 && in_array($key,$cols1)){ ?>
        <a class="example-image-link" img-id="gm-<?php echo $key; ?>" href="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" data-lightbox="example-set" data-title="<?php echo $title; ?>">
          <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" style="width:100%">
        </a>
      <?php
      }else if($cols == 2 && in_array($key,$cols2)){ ?>
        <a class="example-image-link" img-id="gm-<?php echo $key; ?>" href="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" data-lightbox="example-set" data-title="<?php echo $title; ?>">
          <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" style="width:100%">
        </a>
      <?php
      }else if($cols == 3 && in_array($key,$cols3)){ ?>
        <a class="example-image-link" img-id="gm-<?php echo $key; ?>" href="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" data-lightbox="example-set" data-title="<?php echo $title; ?>">
          <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" style="width:100%">
        </a>
      <?php
      }else if($cols == 4 && in_array($key,$cols4)){ ?>
        <a class="example-image-link" img-id="gm-<?php echo $key; ?>" href="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" data-lightbox="example-set" data-title="<?php echo $title; ?>">
          <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" style="width:100%">
        </a>
      <?php  } 
       } 
  }
  ?>