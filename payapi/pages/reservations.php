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
                $package_lebel = $package['package_lebel'];
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
        </style>
        <section>
          <div class="container">
            <div class="row">
              <div class="col-12">
                <h2 class="shoots-Head heading_right"><?=$post_title?></h2>
              </div>
              <div class="col-md-6 reservation">
              <?=$post_description?>
                  <?php if($is_extra == 1) { ?>
                            <h5><?php echo $lang['extra_charges'] ?></h5>
                            <ul class="list-unstyled">
                  <?php 
                  $item = "item_".$_SESSION['lang'];
                              $rows = json_decode($extra_items); 
                              foreach($rows as $row ){
                              echo "<li>- ".$row->$item." ".$row->price." KD.</li>";
                              }
                              ?>
                            </ul>
                            <?php } ?>
                            
                    <h5><?php echo $lang['package_level'] ?></h5>
                    <?php
                      $item = "lebel_".$_SESSION['lang'];
                      $rows = json_decode($package_lebel); 
                      //var_dump($rows);
                      ?>
                      <select class="packagetype_select" name="package_level" id="package_level">
                        <?php 	
                             foreach($rows as $row ){ 
                                          echo "<option value='".$row->$item.' - KD'.$row->price."' data-price='".$row->price."'>".$row->$item.' - KD'.$row->price."</option>";
                            }
                         ?>             
                      </select>
              </div>
              <div class="col-md-6">
                <img src="<?php echo SITEURL; ?>uploads/images/<?=$image_url?>" class="img-rounded img-fluid d-block mx-auto mb-md-0 mb-3">
              </div>

              <div class="col-12 mt-4 reservation-calender-btn">
                <!-- Place somewhere in the <body> of your page -->
                <?php 
                   if($cid==13 ){ ?>
                   <div>
                    <select class="packagetype_select" name="package_category" id="package_category">
                        <?php 
                        $tbl_name = 'tbl_categories';
                        $where = "is_active='Yes'";
                        $query = $obj->select_data($tbl_name,$where);
                        $res = $obj->execute_query($conn,$query);
                        while ($row=$obj->fetch_data($res)) {
                        $cat_id = $row['id']; 
                        $cat_title = $row['title_'.$_SESSION['lang']];
                        ?>
                        <option value="<?php echo $cat_id; ?>" <?=($cid==$cat_id?'selected':'')?>><?php echo $cat_title; ?></option>
                      <?php } ?>            
                  </select>
                </div>  
                  <?php  $themes = get_themes($cid);
                   //var_dump($themes->num_rows);
                   //if(@$themes->num_rows >0 ){ ?>
                    
                <h5 class="shoots-Head heading_right"><?php echo $lang['themes'] ?></h5>
                <div class="wrapper" id="th_result" style="direction:ltr;">
                <div class="res_carousel" id="theme_ul" style="direction:ltr;">
                  <?php
                  if(@$themes->num_rows >0 ){
                      foreach($themes as $key=>$theme){
                   
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
                          <div class="card-body">
                            <div class="card-content">
                              <div class="card-title"><?=$title?></div>
                              <div class="card-text">
                                  <input id="th-<?=$themeid?>" type="radio" name="theme" value="<?=$themeid?>-<?=$title?>" <?=($key==0?'checked':'')?>>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div> 
                    <?php }  }  ?> 
                  </div>
                 </div>
                <?php //}
              } ?>


                <h5 class="shoots-Head heading_right" style="margin-top:35px"><?php echo $lang['session_reservation'] ?></h5>
                <div class="row d-md-flex align-items-end">
                  <div class="col-md-8">
                    <div class="form-group"> <!-- Date input -->
                      <input class="form-control" id="date" name="date" placeholder="MM/DD/YYY" type="text" disabled />
                      <div id="bookingdate"></div>
                    </div>
                  </div>
                  <div class="col-md-4">
                        <ul><li style=" COLOR: #7d807d;"><?php echo $lang['AvailableText'] ?></li>
                            <li style="color: #ea9990;"><?php echo $lang['ReservedText'] ?></li>
                            <li style="color: cornflowerblue;"><?php echo $lang['VacationText'] ?></li>
                        </ul>
                  </div>
                  <div class="col-md-4">
                    <a href="#" class="btn btn-lg btn-outline-primary btn-rounded px-4" id="booknow"><?php echo $lang['book_now'] ?></a>
                  </div>
                </div>

              </div>

              <!-- <div class="col-12 mt-4">
                
              </div> -->

            </div>
          </div>
        </section>
        <!--Package End-->