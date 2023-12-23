<?php 
$userID = $_GET["id"];
$sql = "SELECT c.* , l.* 
		FROM `users` as c
		JOIN `locations` as l
		ON c.id = $userID
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
?>
<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default card-view">
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="form-wrap">
											<form action="#">
												<h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>about User</h6>
												<hr class="light-grey-hr"/>
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label class="control-label mb-10">Name</label>
															<input type="text" id="enname" class="form-control" value="<?php echo $row["fullName"];?>">
														</div>
													</div>
													<!--/span-->
													<div class="col-md-6">
														<div class="form-group">
															<label class="control-label mb-10">E-mail</label>
															<input type="text" id="arname" class="form-control" value="<?php echo $row["email"];?>">
														</div>
													</div>
													<!--/span-->
												</div>
												
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label class="control-label mb-10">Phone</label>
															<input type="text" id="enname" class="form-control" value="<?php echo $row["phone"];?>">
														</div>
													</div>
													<!--/span-->
													<div class="col-md-6">
														<div class="form-group">
															<label class="control-label mb-10">Joinging Date</label>
															<input type="text" id="arname" class="form-control" value="<?php $date = explode(" ",$row["date"]); echo $date[0];?>">
														</div>
													</div>
													<!--/span-->
												</div>
												
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label class="control-label mb-10">Country</label>
															<input type="text" id="enname" class="form-control" value="<?php echo $row["country"];?>">
														</div>
													</div>
													<!--/span-->
													<div class="col-md-6">
														<div class="form-group">
															<table style="width:100%"><tr><td><label class="control-label mb-10">Address 1</label></td><td><label class="control-label mb-10">Address 1</label></td></tr><tr><td>
															<input type="text" id="arname" class="form-control" value="<?php echo $row["address_1"];?>"></td><td><input type="text" id="arname" class="form-control" value="<?php echo $row["address_2"];?>"></td></tr></table>
														</div>
													</div>
													<!--/span-->
												</div>
												
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label class="control-label mb-10">City</label>
															<input type="text" id="enname" class="form-control" value="<?php echo $row["city"];?>">
														</div>
													</div>
													<!--/span-->
													<div class="col-md-6">
														<div class="form-group">
															<label class="control-label mb-10">Postal Code</label>
															<input type="text" id="arname" class="form-control" value="<?php echo $row["zipcode"];?>">
														</div>
													</div>
													<!--/span-->
												</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>