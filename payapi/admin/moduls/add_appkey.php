
	<!-- Title -->
		<div class="row heading-bg">
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<h5 class="txt-dark"><?php echo $lang['add_theme'] ?></h5>

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

							<?php 
								if(isset($_GET['cid']) && !empty($_GET['cid']))
								{
									$cid = $_GET['cid'];
									
								}
								if(isset($_SESSION['add']))
								{
									echo $_SESSION['add'];
									unset($_SESSION['add']);
								}
							?>

							<form method="post" action="" enctype='multipart/form-data' >
						
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left"><?php echo $lang['title'] ?> (<?php echo $lang['english'] ?>)</label> 
									<input type="text" name="title_en" placeholder="Theme Title in English" required="true" class="form-control">
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left"><?php echo $lang['title'] ?> (<?php echo $lang['arabic'] ?>)</label>
									<input type="text" name="title_ar" placeholder="Theme Title in Arabic" class="form-control">
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Api Provider</label>
									<select name="provider" class="form-control" id="api_provider">
									    <option value="myfatoorah">MyFatoorah</option>
										<option value="upayment">UInterface</option>
									    <option value="bookeey">Bookeey</option>
									</select> 
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Api Mode</label>
									<select name="mode" class="form-control">
									    <option value="test">Test</option>
									    <option value="live">Live</option>
									</select> 
								</div>
								
								
								<div class="form-group col-md-12 col-sm-12" id="myfatoorah-div" style="display:block">
									<label class="control-label mb-10 text-left">Token</label>
									<textarea name="token" placeholder="Myfatoorah token for payment" class="form-control"></textarea>
								</div>

								<div id="upayment-div" style="display:none">
									<div class="form-group col-md-6 col-sm-12">
										<label class="control-label mb-10 text-left">UInterface Merchant ID</label>
										<input type="text" name="uMerchantID" placeholder="Bookeey Merchant ID" class="form-control">
									</div>
									<div class="form-group col-md-6 col-sm-12">
										<label class="control-label mb-10 text-left"> UInterface Username</label>
										<input type="text" name="username" placeholder=" UInterface Username" class="form-control">
									</div>
									<div class="form-group col-md-6 col-sm-12">
										<label class="control-label mb-10 text-left">UInterface password</label>
										<input type="text" name="password" placeholder="UInterface password" class="form-control">
									</div>
									<div class="form-group col-md-6 col-sm-12">
										<label class="control-label mb-10 text-left">UInterface Api Key</label>
										<input type="text" name="uApikey" placeholder="UInterface Api Key" class="form-control">
									</div>
								</div>
								<div id="bookeey-div" style="display:none">
									<div class="form-group col-md-6 col-sm-12">
										<label class="control-label mb-10 text-left">Merchant ID</label>
										<input type="text" name="MerchantID" placeholder="Bookeey Merchant ID" class="form-control">
									</div>
									<div class="form-group col-md-6 col-sm-12">
										<label class="control-label mb-10 text-left">Secret Key</label>
										<input type="text" name="SecretKey" placeholder="Bookeey Secret Key" class="form-control">
									</div>
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Site URL</label>
									<input type="text" name="SiteUrl" placeholder="Site url " class="form-control">
								</div>
                                <div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">
									<input type="hidden" name="visamaster_deduction" value="0">
									<input type="checkbox" name="visamaster_deduction" value="1">
									Visa master Charges </label>
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Visa master Charges(%)</label>
									<input type="text" name="visamaster_charges" placeholder="Charges 2.5" class="form-control">
								</div>
								
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Customer Reference</label>
									<input type="text" name="CustomerReference" placeholder="Customer Reference" class="form-control">
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Shipping Method</label>
									<input type="text" name="ShippingMethod" placeholder="Shipping Method" class="form-control">
								</div>
								
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Supplier Code</label>
									<input type="text" name="SupplierCode" placeholder="SupplierCode" class="form-control">
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Source Info</label>
									<input type="text" name="SourceInfo" placeholder="Source Info" class="form-control">
								</div>
								
								<div class="form-group col-md-12 col-sm-12">
									<label class="control-label mb-10 text-left">Vendor Name</label>
									<input type="text" name="vendorName" placeholder="Vendor Name" class="form-control" value="">
								</div>
								
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Charges</label>
									<input type="text" name="chargeAmount" placeholder="Charges" class="form-control" value="">
								</div>
								
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">Charge Type</label>
									<select name="chargeType" class="form-control">
    									 <option value="fixed" >Fixed</option>
    									 <option value="percentage" >Percentage</option>
    								 </select>
								</div>
								
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">CC Charges</label>
									<input type="text" name="cc_charge" placeholder="Charges" class="form-control" value="">
								</div>
								
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">CC Charge Type</label>
									<select name="cc_chargetype" class="form-control">
    									 <option value="fixed" >Fixed</option>
    									 <option value="percentage" >Percentage</option>
    								 </select>
								</div>
								
								<div class="form-group col-md-12 col-sm-12">
									<label class="control-label mb-10 text-left">Vendor IBAN/Marchent ID</label>
									<input type="text" name="ibanMarchent" placeholder="Vendor IBAN/Marchent ID" class="form-control" value="">
								</div>

								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">DB Host</label>
									<input type="text" name="db_host" placeholder="DB Host " class="form-control">
								</div>

								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">DB Username</label>
									<input type="text" name="db_user" placeholder="DB Username " class="form-control">
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">DB Password</label>
									<input type="text" name="db_pass" placeholder="DB Password " class="form-control">
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10 text-left">DB Name</label>
									<input type="text" name="db_name" placeholder="DB Name" class="form-control">
								</div>
							
								<div class="form-group col-md-6 col-sm-12">
									<label class="control-label mb-10"><?php echo $lang['is_active'] ?></label>
									<div>
										<div class="radio">
											<input type="radio" name="is_active" id="radio_1" value="Yes">
											<label for="radio_1">
											<?php echo $lang['yes'] ?>
											</label>
										</div>
										<div class="radio">
											<input type="radio" name="is_active" id="radio_2" value="No" >
											<label for="radio_2">
											<?php echo $lang['no'] ?>
											</label>
										</div>
									</div>
								</div>
								
								


								<div class="form-group col-md-12 col-sm-12">
									<span class="input-label">
										<input type="submit" name="submit" value="Add Now" class="btn-primary btn-sm">
									</span>
								</div>
								<br>
							</form>
							</div>
							</div>
							</div>

							<?php 
		if(isset($_POST['submit']))
		{
			//echo "Click";
			$title_en = $obj->sanitize($conn,$_POST['title_en']);
			$title_ar = $obj->sanitize($conn,$_POST['title_ar']);
			$uMerchantID = $obj->sanitize($conn,$_POST['uMerchantID']);
			$username = $obj->sanitize($conn,$_POST['username']);
			$password = $obj->sanitize($conn,$_POST['password']);
			$uApikey = $obj->sanitize($conn,$_POST['uApikey']);
			$MerchantID = $obj->sanitize($conn,$_POST['MerchantID']);
			$SecretKey = $obj->sanitize($conn,$_POST['SecretKey']);
			$provider = $obj->sanitize($conn,$_POST['provider']);
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
			$mode = $_POST['mode'];;
			$token = $_POST['token'];;
			
			$is_active = $_POST['is_active'];
            $apikey='CKW-'.time().'-'.mt_rand(1000,9999);
			$created_at = date('Y-m-d H:i:s');
			$tbl_name = 'tbl_apikeys';
			$data= "
				title_en = '$title_en',
				title_ar = '$title_ar',
				provider = '$provider',
				api_key = '$apikey',
				SiteUrl = '$SiteUrl',
				CustomerReference = '$CustomerReference',
				visamaster_deduction = '$visamaster_deduction',
				visamaster_charges = '$visamaster_charges',
				ShippingMethod = '$ShippingMethod',
				SupplierCode = '$SupplierCode',
				SourceInfo = '$SourceInfo',
				uMerchantID = '$uMerchantID',
				username = '$username',
				password = '$password',
				uApikey = '$uApikey',
				MerchantID = '$MerchantID',
				SecretKey = '$SecretKey',
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
				is_active = '$is_active',
				created_at = '$created_at'
			";
			$query = $obj->insert_data($tbl_name,$data);
			$res = $obj->execute_query($conn,$query);

			if($res==true)
			{
				//Category Successfully Added
				$_SESSION['add'] = "<div class='success'>".$lang['add_success']."</div>";
				header('location:'.SITEURL.'admin/index.php?page=appkey');
			}
			else
			{
				//Failed to Add Categoy
				$_SESSION['add'] = "<div class='error'>".$lang['add_fail']."</div>";
				header('location:'.SITEURL.'admin/index.php?page=appkey');
			}
		}
	?>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel panel-default card-view">
		</div>
	</div>
</div>
					<!-- /Row -->
