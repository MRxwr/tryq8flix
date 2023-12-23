          
          <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['customer_name'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" class="form-control form-control-lg" id="customer_name" name="customer_name" required >
              </div>
            </div>

            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['business_name'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" class="form-control form-control-lg" id="business_name" name="business_name" required >
              </div>
            </div>

            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['business_type'] ?>:</label>
              <div class="col-sm-7 col-md-8">
              <select class="form-control form-control-lg" id="business_type" name="business_type" style="max-width: 300px;" required>
                <option value=""  ><?php echo $lang['business_type'] ?></option>
                <?php $tbl_name = 'tbl_business_type';
                      $where = "is_active='Yes'";
                      $query = $obj->select_data($tbl_name,$where);
                      $res = $obj->execute_query($conn,$query);
                      while ($btype=$obj->fetch_data($res)) {
                        $title = $btype['title_'.$_SESSION['lang']];?>
                        <option value="<?=$btype['id']?>-<?php echo $title; ?>"><?php echo $title; ?></option>
                    <?php } ?>
                    <option value="0" >Other</option>
                </select>
                
              </div>
            </div>
            <div class="form-group row" style="display:none;" id="x_business_type" >
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['business_type'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" class="form-control form-control-lg" id="other_business_type" name="other_business_type" value="" placeholder="Optional" >
              </div>
            </div>
            <div class="form-group row" >
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['customer_email'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="email" class="form-control form-control-lg" id="customer_email" name="customer_email" value="" placeholder="Optional" >
              </div>
            </div>

            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['mobile_number'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <input type="text" class="form-control form-control-lg" id="mobile_number" name="mobile_number" required>
              </div>
            </div>
            

            <div class="form-group row">
              <label for="" class="col-sm-5 col-md-4 col-form-label"><?php echo $lang['notes'] ?>:</label>
              <div class="col-sm-7 col-md-8">
                <textarea name="instructions" id="instructions" class="form-control form-control-lg"  rows="4" placeholder=""></textarea>
              </div>
            </div>
