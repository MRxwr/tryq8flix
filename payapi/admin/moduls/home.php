<div class="body">
	<?php if(isset($_SESSION['login']))
		{
			//echo $_SESSION['login'];
			//unset($_SESSION['login']);
		}
	?>
	<h2><?php //echo $lang['welcome'] ?></h2>
	<br>
	<p>
		<?php //echo $lang['welcome_message'] ?>
	</p>
</div>
<div class="container-fluid pt-25">
				<!-- Row -->
				<div class="row">
				    <?php 
					 if($user_id==1){
						$tbl_name = 'tbl_apikeys';
						$where = " is_active='Yes'";
						$query = $obj->select_data($tbl_name,$where);
						$res = $obj->execute_query($conn,$query);
						$count_rows= $obj->num_rows($res);
						$total_booking = $count_rows;
						
						?>
					<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
						<div class="panel panel-default card-view pa-0">
							<div class="panel-wrapper collapse in">
								<div class="panel-body pa-0">
									<div class="sm-data-box">
										<div class="container-fluid">
											<div class="row">
												<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
													<span class="txt-dark block counter"><span class="counter-anim"><?=$total_booking?></span></span>
													<span class="weight-500 uppercase-font block font-13"><?php echo $lang['total_booking_success'] ?></span>
												</div>
												<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
													<i class="icon-user-following data-right-rep-icon txt-light-grey"></i>
												</div>
											</div>	
										</div>
									</div>
								</div>
							</div>
						</div>
					</div> 
					<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
						<div class="panel panel-default card-view pa-0">
							<div class="panel-wrapper collapse in">
								<div class="panel-body pa-0">
									<div class="sm-data-box">
										<div class="container-fluid">
											<div class="row">
												<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
													<span class="txt-dark block counter"><span class="counter-anim"><?=$total_booking?></span>KD</span>
													<span class="weight-500 uppercase-font block"><?php echo $lang['total_earning'] ?></span>
												</div>
												<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
													<i class="icon-graph  data-right-rep-icon txt-light-grey"></i>
												</div>
											</div>	
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				
						<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
						<div class="panel panel-default card-view pa-0">
							<div class="panel-wrapper collapse in">
								<div class="panel-body pa-0">
									<div class="sm-data-box">
										<div class="container-fluid">
											<div class="row">
												<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
													<span class="txt-dark block counter"><span class="counter-anim"><?=$total_booking?></span></span>
													<span class="weight-500 uppercase-font block"><?php echo $lang['total_booking_completed'] ?></span>
												</div>
												<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
													<i class="icon-layers data-right-rep-icon txt-light-grey"></i>
												</div>
											</div>	
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<?php }else{ ?>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						 <h3 class="align-center">Welcome to Haya Photography Dashboard</h3>
						</div>
					<?php } ?>
				</div>
				<!-- Row -->
			</div>