
	  
<style>
  #slider2{
    width:100%;
    margin-top:15px;
  }
  .flexslider {
  margin: 0 0 60px;
  background: #fff;
  border: 4px solid #fff;
  position: relative;
  zoom: 1;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
  -webkit-box-shadow: '' 0 1px 4px rgba(0, 0, 0, 0.2);
  -moz-box-shadow: '' 0 1px 4px rgba(0, 0, 0, 0.2);
  -o-box-shadow: '' 0 1px 4px rgba(0, 0, 0, 0.2);
  box-shadow: '' 0 1px 4px rgba(0, 0, 0, 0.2);
}
.flexslider .slides img {
    width: 98%;
    display: block;
}
  .flex-caption {
  width: 96%;
  padding: 2%;
  left: 0;
  bottom: 0;
  background: rgba(0,0,0,.5);
  color: #fff;
  text-shadow: 0 -1px 0 rgba(0,0,0,.3);
  font-size: 14px;
  line-height: 18px;
}
li.css a {
  border-radius: 0;
}
 
 .album_title{
   margin:10px auto;
   text-align:center!important;
 }
 .album_title h3{
  text-align:center!important;
 }
.btn-outline-secondary{
  background-color: #fdcbec;
  color: #fff;
  border-radius: 10px;
  margin: 20px auto;
 position: relative;
  left: 36%;
}
.btn-outline-secondarygl{
  background-color: #fdcbec;
  color: #fff;
  border-radius: 10px;
  margin: 20px auto;
 position: relative;
  left: 15%;
}
.btn-outline-secondary {
    color: #fff;
    border-color: #fdcbec;
}
.btn-outline-secondary:hover {
    color: #fff;
    background-color: #fdcbec;
    border-color: #fdcbec;
}
</style>

  <section class="gallery_panel">
    <div class="container">
      <div class="row">
      <?php 
		
		if(isset($_GET['id'])){ ?>

      <div class="col-12 text-center">
            <!-- <button class="btn btn-sm theme-bg text-uppercase text-white" onclick="one()">Single Grid</button> -->
            <!-- <button class="btn btn-sm theme-bg text-uppercase text-white" onclick="two()">Double Grid</button>
            <button class="btn btn-sm theme-bg text-uppercase text-white active" onclick="four()">4 Grids</button> -->
        </div>
        <div class="col-lg-10 col-md-10 mx-auto">
          <div class="mt-3 gallery-grid" style="direction:ltr;">          
              <?php $galleries=get_galleries($_GET['id']); 
                  if(!empty($galleries)){ ?>  
                  <div id="slider1" class="flexslider" style="orverflow:hidden">
                   <ul class="slides">
                      <?php foreach($galleries as $key=>$gallery){
                          $title = $gallery['title_'.$_SESSION['lang']];
                          $image_url = $gallery['image_url'];
                          ?>
                          <li>
                               <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" >
                           </li>
                        <?php } ?>
                      </ul>
                  </div>
                  <div id="slider2" class="flexslider">
                   <ul class="slides">
                      <?php foreach($galleries as $key=>$gallery){
                          $title = $gallery['title_'.$_SESSION['lang']];
                          $image_url = $gallery['image_url'];
                          ?>
                          <li style="width:90px; height:90px n ">
                               <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" >
                           </li>
                        <?php } ?>
                      </ul>
                  </div>

                  <div style="text-align:center; margin: 10px auto">
                  <a href="<?php echo SITEURL; ?>index.php?page=categories" class="btn btn-lg btn-outline-secondarygl "><?php echo $lang['BookNow'] ?></a>
                 </div>

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
                        <div class="col-xs-12 col-sm-12 col-lg-4 col-md-4 text-center; margin-bottom:20px;">
                          <a class="example-image-link" href="<?php echo SITEURL; ?>index.php?page=galleries&id=<?=$id?>">
                            <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" style="width:100%">
                            <div class="album_title">
                            <h3><?=$title?></h3></div>
                          </a>
                           <a href="<?php echo SITEURL; ?>index.php?page=categories" class="btn btn-lg btn-outline-secondary"><?php echo $lang['BookNow'] ?></a>
                        </div>
                      <?php  } ?>
                    </div>
                  </div>

    <?php } ?>
           
      </div>
    </div>  
  </section>
  