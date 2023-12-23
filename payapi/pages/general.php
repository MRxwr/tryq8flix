
          <?php if(checkRamadanDate($bkdate)){ ?>
                  <div class="form-group row">
                        <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['ramadan'] ?>:</label>
                        <div class="col-sm-7 col-md-8">
                          <select class="form-control form-control-lg" id="booking_time" name="booking_time" style="max-width: 300px;" required>
                          <option value=""  ><?php echo $lang['select_time'] ?></option>
                            <?php 
                              $timestat =0;
                              $tbl_name = 'tbl_ramadan';
                              $where = "is_active='Yes'";
                              $query = $obj->select_data($tbl_name,$where);
                                $res = $obj->execute_query($conn,$query);
                                while ($row=$obj->fetch_data($res)) {
                                $slot = $row['slot']; 
                                if (!in_array($slot, $booktimeArr) && !in_array($slot,$disable_times)){ $timestat =1 ?>
                                <option value="<?php echo $slot; ?>"><?php echo $slot; ?></option>
                              <?php } } ?>
                          </select>
                        </div>
                      </div>
          <?php }else{ ?>
                  <div class="form-group row">
                        <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['preffered_time'] ?>:</label>
                        <div class="col-sm-7 col-md-8">
                        <select class="form-control form-control-lg" id="booking_time" name="booking_time" style="max-width: 300px;">
                          <option value=""  ><?php echo $lang['select_time'] ?></option>
                            <?php 
                              $timestat =0;
                              $tbl_name = 'tbl_timeslots';
                              $where = "is_active='Yes'";
                              $query = $obj->select_data($tbl_name,$where);
                                $res = $obj->execute_query($conn,$query);
                                while ($row=$obj->fetch_data($res)) {
                                $slot = $row['slot']; 
                                if (!in_array($slot, $booktimeArr) && !in_array($slot,$disable_times)){ ?>
                                <option value="<?php echo $slot; ?>"><?php echo $slot; ?></option>
                              <?php } } ?>
                          </select>
                        </div>
                      </div>

            <?php } ?>   

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
              foreach($extra_items_rows as $extra_items_row ){ ?>
                <div class="form-check">
                  <input class="form-check-input xprice" type="checkbox" data-exprice="<?php echo $extra_items_row->price; ?>" value="<?php echo $extra_items_row->$item.",".$extra_items_row->price; ?>" name="select_extra_item[]">
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
                <input type="text" class="form-control form-control-lg" id="baby_name" name="baby_name" placeholder="Optional">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['baby_age'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" class="form-control form-control-lg" id="baby_age" name="baby_age" placeholder="Optional">
              </div>
            </div>

            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['photography_type'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <textarea name="instructions" id="instructions" class="form-control form-control-lg"  rows="4" placeholder="Example: ( birthday, Anniversary, Pregnancy, Graduation)"></textarea>
              </div>
            </div>
