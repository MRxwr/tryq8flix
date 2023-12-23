        <?php 
        include('../languages/lang_config.php');
        include('../admin/config/apply.php');
        include('../includes/functions.php');
        ?>

               <!-- Place somewhere in the <body> of your page -->
                <?php $themes = get_themes($_GET['cid']);
                   //var_dump($themes->num_rows);
                  if(@$themes->num_rows >0 ){ ?>
                    <div class="res_carousel" style="direction:ltr;">
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
                        <?php }   ?> 
                  </div>
                <?php } ?>
