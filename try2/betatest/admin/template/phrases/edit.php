<?php
include_once ("includes/config.php");
$sql= "SELECT * FROM phrases WHERE id LIKE '".$_GET["id"]."'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
?>


<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default card-view">
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="form-wrap">
											<form action="includes/phrases/edit.php?id=<?php echo $_GET["id"] ?>" method="POST">
												<h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>about phrase</h6>
												<hr class="light-grey-hr"/>
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label class="control-label mb-10">English phrase</label>
															<input type="text" name="enphr" class="form-control" value='<?php echo $row["phr-en"] ?>'>
														</div>
													</div>
													<!--/span-->
													<div class="col-md-6">
														<div class="form-group">
															<label class="control-label mb-10">Arabic phrase</label>
															<input type="text" name="arphr" class="form-control" value='<?php echo $row["phr-ar"] ?>'>
														</div>
													</div>
													<!--/span-->
												</div>
												<div class="seprator-block"></div>
												<div class="form-actions">
													<button class="btn btn-success btn-icon left-icon mr-10 pull-left"> <i class="fa fa-check"></i> <span>save</span></button>
													<div class="clearfix"></div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>