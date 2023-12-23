<?php 
		if(isset($_POST['submit']))
		{
			//echo "Click";
			$id = $_POST['id'];
			$title_en = $obj->sanitize($conn,$_POST['title_en']);
			$title_ar = $obj->sanitize($conn,$_POST['title_ar']);
			$provider = $obj->sanitize($conn,$_POST['provider']);
			$uMerchantID = $obj->sanitize($conn,$_POST['uMerchantID']);
			$username = $obj->sanitize($conn,$_POST['username']);
			$password = $obj->sanitize($conn,$_POST['password']);
			$uApikey = $obj->sanitize($conn,$_POST['uApikey']);
			$MerchantID = $obj->sanitize($conn,$_POST['MerchantID']);
			$SecretKey = $obj->sanitize($conn,$_POST['SecretKey']);
			$SiteUrl = $obj->sanitize($conn,$_POST['SiteUrl']);
			$CustomerReference = $obj->sanitize($conn,$_POST['CustomerReference']);
			$ShippingMethod = $obj->sanitize($conn,$_POST['ShippingMethod']);
			$SupplierCode = $obj->sanitize($conn,$_POST['SupplierCode']);
			$SourceInfo = $obj->sanitize($conn,$_POST['SourceInfo']);
			
			$vendorName= $obj->sanitize($conn,$_POST['vendorName']);
			$chargeAmount = $obj->sanitize($conn,$_POST['chargeAmount']);
			$chargeType = $obj->sanitize($conn,$_POST['chargeType']);
			$cc_charge = $obj->sanitize($conn,$_POST['cc_charge']);
			$cc_chargetype = $obj->sanitize($conn,$_POST['cc_chargetype']);
			$ibanMarchent = $obj->sanitize($conn,$_POST['ibanMarchent']);
			
			
			$db_host = $obj->sanitize($conn,$_POST['db_host']);
			$db_user = $obj->sanitize($conn,$_POST['db_user']);
			$db_pass = $obj->sanitize($conn,$_POST['db_pass']);
			$db_name = $obj->sanitize($conn,$_POST['db_name']);
			$visamaster_deduction = $obj->sanitize($conn,$_POST['visamaster_deduction']);
			$visamaster_charges = $obj->sanitize($conn,$_POST['visamaster_charges']);
			$is_active = $_POST['is_active'];
			$mode= $_POST['mode'];
			$token = $_POST['token'];
			$tbl_name = 'tbl_apikeys';
			$data= "
			    title_en = '$title_en',
				title_ar = '$title_ar',
				provider = '$provider',
				uMerchantID = '$uMerchantID',
				username = '$username',
				password = '$password',
				uApikey = '$uApikey',
				MerchantID = '$MerchantID',
				SecretKey = '$SecretKey',
				SiteUrl = '$SiteUrl',
				CustomerReference = '$CustomerReference',
				visamaster_deduction = '$visamaster_deduction',
				visamaster_charges = '$visamaster_charges',
				ShippingMethod = '$ShippingMethod',
				SupplierCode = '$SupplierCode',
				SourceInfo = '$SourceInfo',
				vendorName = '$vendorName',
				chargeAmount = '$chargeAmount',
				chargeType = '$chargeType',
				cc_charge = '$cc_charge',
				cc_chargetype = '$cc_chargetype',
				ibanMarchent = '$ibanMarchent',
				db_host = '$db_host',
				db_user = '$db_user',
				db_pass = '$db_pass',
				db_name = '$db_name',
				mode = '$mode',
				token = '$token',
				is_active = '$is_active'
				";
			$where = "id='$id'";	
			$query = $obj->update_data($tbl_name,$data,$where);
			$res = $obj->execute_query($conn,$query);
			if($res==true)
			{
				//Category Successfully Added
				$_SESSION['add'] = "<div class='success'>".$lang['edit_success']."</div>";
				header('location:'.SITEURL.'admin/index.php?page=appkey');
			}
			else
			{
				//Failed to Add Categoy
				$_SESSION['add'] = "<div class='error'>".$lang['edit_fail']."</div>";
				header('location:'.SITEURL.'admin/index.php?page=appkey&id='.$id);
			}
		}
	?>
	<?php 
		if(isset($_SESSION['edit']))
		{
			echo $_SESSION['edit'];
			unset($_SESSION['edit']);
		}
		
		if(isset($_GET['id']) && !empty($_GET['id']))
		{
			$id = $_GET['id'];
			$tbl_name ='tbl_apikeys';
			$where = "id='$id'";

			$query = $obj->select_data($tbl_name,$where);
			$res = $obj->execute_query($conn,$query);
			if($res)
			{
				$count_rows = $obj->num_rows($res);
				if ($count_rows==1) {
					$row = $obj->fetch_data($res);
					$title_en = $row['title_en'];
					$title_ar = $row['title_ar'];
					$provider =  $row['provider'];
					$uMerchantID = $row['uMerchantID'];
					$username = $row['username'];
					$password = $row['password'];
					$uApikey = $row['uApikey'];
					$MerchantID = $row['MerchantID'];
					$SecretKey = $row['SecretKey'];
					$api_key =  $row['api_key'];
					$mode =  $row['mode'];
					$token =  $row['token'];
					$SiteUrl =  $row['SiteUrl'];
					$CustomerReference =  $row['CustomerReference'];
					$ShippingMethod =  $row['ShippingMethod'];
					$SupplierCode =  $row['SupplierCode'];
					$SourceInfo =  $row['SourceInfo'];
					$vendorName =  $row['vendorName'];
					$chargeAmount =  $row['chargeAmount'];
					$chargeType =  $row['chargeType'];
					$cc_charge =  $row['cc_charge'];
					$cc_chargetype =  $row['cc_chargetype'];
					$ibanMarchent =  $row['ibanMarchent'];
					
					$db_host =  $row['db_host'];
					$db_user =  $row['db_user'];
					$db_pass =  $row['db_pass'];
					$db_name =  $row['db_name'];
					$is_active = $row['is_active'];
                    $visamaster_deduction = $row['visamaster_deduction'];
					$visamaster_charges = $row['visamaster_charges'];
				}
			}
		}
		else
		{
			header('location:'.SITEURL.'admin/index.php?page=appkey');
		}
	?>
<!-- Title -->
    <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
          <h5 class="txt-dark"><?php echo $lang['edit_theme'] ?></h5>
        </div>
    </div>
    <!-- /Title -->

<!-- Row -->
    <div class="row">
        <div class="col-sm-8">
            <div class="panel panel-default card-view">
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="form-wrap">
                                <form method="post" action="" enctype="multipart/form-data">
        							<div class="form-group col-md-6 col-sm-12">
                                        <label class="control-label mb-10 text-left"><?php echo $lang['title'] ?> (<?php echo $lang['english'] ?>)</label>
                                        <input type="text" name="title_en" value="<?php echo $title_en; ?>" required="true" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label class="control-label mb-10 text-left"><?php echo $lang['title'] ?> (<?php echo $lang['arabic'] ?>)</label>
                                        <input type="text" name="title_ar" value="<?php echo $title_ar; ?>" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12">
										<label class="control-label mb-10 text-left">Api Provider</label>
										<select name="provider" class="form-control"  id="api_provider">
											<option value="myfatoorah" <?=($provider=='myfatoorah'?'selected="selected"':'')?>>MyFatoorah</option>
											<option value="upayment" <?=($provider=='upayment'?'selected="selected"':'')?>>UInterface</option>
											<option value="bookeey" <?=($provider=='bookeey'?'selected="selected"':'')?>>Bookeey</option>
										</select> 
									</div>
                                    <div class="form-group col-md-6 col-sm-12">
    								  <label class="control-label mb-10 text-left">Api Mode</label>
    								  <select name="mode" class="form-control">
    									 <option value="test" <?=($mode=='test'?'selected="selected"':'')?>>Test</option>
    									 <option value="live" <?=($mode=='live'?'selected="selected"':'')?>>Live</option>
    								  </select> 
    								</div>
    								
    								<div class="form-group col-md-12 col-sm-12"  id="myfatoorah-div" style="display:<?=($provider=='myfatoorah'?'block':'none')?>">
    									<label class="control-label mb-10 text-left">Token</label>
    									<textarea name="token" placeholder="Myfatoorah token for payment" class="form-control"><?=$token?></textarea>
    								</div>
									<div id="upayment-div" style="display:<?=($provider=='upayment'?'block':'none')?>" >
									<div class="form-group col-md-6 col-sm-12">
										<label class="control-label mb-10 text-left">UInterface Merchant ID</label>
										<input type="text" name="uMerchantID" placeholder="Bookeey Merchant ID" value="<?=$uMerchantID?>" class="form-control">
									</div>
									<div class="form-group col-md-6 col-sm-12">
										<label class="control-label mb-10 text-left"> UInterface Username</label>
										<input type="text" name="username" placeholder=" UInterface Username" value="<?=$username?>" class="form-control">
									</div>
									<div class="form-group col-md-6 col-sm-12">
										<label class="control-label mb-10 text-left">UInterface password</label>
										<input type="text" name="password" placeholder="UInterface password" value="<?=$password?>" class="form-control">
									</div>
									<div class="form-group col-md-6 col-sm-12">
										<label class="control-label mb-10 text-left">UInterface Api Key</label>
										<input type="text" name="uApikey" placeholder="UInterface Api Key" value="<?=$uApikey?>"  class="form-control">
									</div>
								</div>
								<div id="bookeey-div" style="display:<?=($provider=='bookeey'?'block':'none')?>">
									<div class="form-group col-md-6 col-sm-12">
										<label class="control-label mb-10 text-left">Merchant ID</label>
										<input type="text" name="MerchantID" placeholder="Bookeey Merchant ID" value="<?=$MerchantID?>" class="form-control">
									</div>
									<div class="form-group col-md-6 col-sm-12">
										<label class="control-label mb-10 text-left">Secret Key</label>
										<input type="text" name="SecretKey" placeholder="Bookeey Secret Key" value="<?=$SecretKey?>" class="form-control">
									</div>
								</div>
								<div class="form-group col-md-6 col-sm-12">
									  <label class="control-label mb-10 text-left">Site URL</label>
									  <input type="text" name="SiteUrl" placeholder="Site url " class="form-control" value="<?=$SiteUrl?>">
								   </div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">
									<input type="hidden" name="visamaster_deduction" value="0">
									<input type="checkbox" name="visamaster_deduction" value="1" <?=($visamaster_deduction==1?'checked':'')?> >
									Visa master deduction</label>
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Visa master Charges(%)</label>
									<input type="text" name="visamaster_charges" placeholder="Charges 2.5" value="<?=$visamaster_charges?>" class="form-control">
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Customer Reference</label>
									<input type="text" name="CustomerReference" placeholder="Customer Reference" class="form-control" value="<?=$CustomerReference?>">
								</div>
							
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Shipping Method</label>
									<input type="text" name="ShippingMethod" placeholder="Shipping Method" class="form-control" value="<?=$ShippingMethod?>">
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Supplier Code</label>
									<input type="text" name="SupplierCode" placeholder="Supplier Code" class="form-control" value="<?=$SupplierCode?>">
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Source Info</label>
									<input type="text" name="SourceInfo" placeholder="Source Info" class="form-control" value="<?=$SourceInfo?>">
								</div>
								
								
								<div class="form-group col-md-12 col-sm-12">
									<label class="control-label mb-10 text-left">Vendor Name</label>
									<input type="text" name="vendorName" placeholder="Vendor Name" class="form-control" value="<?=$vendorName?>">
								</div>
								
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Charges</label>
									<input type="text" name="chargeAmount" placeholder="Charges" class="form-control" value="<?=$chargeAmount?>">
								</div>
								
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Charge Type</label>
									<select name="chargeType" class="form-control">
    									 <option value="fixed" <?=($chargeType=='fixed'?'selected="selected"':'')?>>Fixed</option>
    									 <option value="percentage" <?=($chargeType=='percentage'?'selected="selected"':'')?>>Percentage</option>
    								 </select>
								</div>
								
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">CC Charges</label>
									<input type="text" name="cc_charge" placeholder="CC Charges" class="form-control" value="<?=$cc_charge?>">
								</div>
								
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">CC Charge Type</label>
									<select name="cc_chargetype" class="form-control">
    									 <option value="fixed" <?=($cc_chargetype=='fixed'?'selected="selected"':'')?>>Fixed</option>
    									 <option value="percentage" <?=($cc_chargetype=='percentage'?'selected="selected"':'')?>>Percentage</option>
    								 </select>
								</div>
								
								<div class="form-group col-md-12 col-sm-12">
									<label class="control-label mb-10 text-left">Vendor IBAN/Marchent ID</label>
									<input type="text" name="ibanMarchent" placeholder="Vendor IBAN/Marchent ID" class="form-control" value="<?=$ibanMarchent?>">
								</div>

								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">DB Host</label>
									<input type="text" name="db_host" placeholder="DB Host " class="form-control" value="<?=$db_host?>">
								</div>

								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">DB Username</label>
									<input type="text" name="db_user" placeholder="DB Username " class="form-control" value="<?=$db_user?>">
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">DB Password</label>
									<input type="text" name="db_pass" placeholder="DB Password " class="form-control" value="<?=$db_pass?>">
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">DB Name</label>
									<input type="text" name="db_name" placeholder="DB Name" class="form-control" value="<?=$db_name?>" >
								</div>

        							<div class="form-group col-md-6 col-sm-12">
        							<label class="control-label mb-10"><?php echo $lang['is_active'] ?></label>
        							<div>
        								<div class="radio">
        									<input type="radio" name="is_active" id="radio_1" value="Yes" <?php if($is_active=='Yes'){echo "checked='checked'";} ?> >
        									<label for="radio_1">
        									<?php echo $lang['yes'] ?>
        									</label>
        								</div>
        								<div class="radio">
        									<input type="radio" name="is_active" id="radio_2" value="No"  <?php if($is_active=='No'){echo "checked='checked'";} ?>>
        									<label for="radio_2">
        									<?php echo $lang['no'] ?>
        									</label>
        								</div>
        							  </div>
        						    </div>
                                    <div class="input-group col-md-12 col-sm-12">
                                        <span class="input-label">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <input type="submit" name="submit" value="Update Now" class="btn-primary btn-sm">
                                        </span>
                                    </div>
                                    <br>
                                  </form>
                                 </div>
								</div>
							</div>
                           </div>
						</div>
						<div class="col-sm-4">
							<div class="panel panel-default card-view">
							</div>
						</div>
					</div>
					<!-- /Row -->	
	