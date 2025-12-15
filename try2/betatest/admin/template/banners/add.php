<?php 
if ( isset($_GET["b"]) )
{
	if ( $_GET["b"] == "new" )
	{
		$bannerType = "includes/banners/add.php?b=new";
	}
	elseif ( $_GET["b"] == "best" )
	{
		$bannerType = "includes/banners/add.php?b=best";
	}
	elseif ( $_GET["b"] == "build" )
	{
		$bannerType = "includes/banners/add.php?b=build";
	}
}
else
{
	$bannerType = "includes/banners/add.php?b=main";
}
?>
<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default card-view">
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="form-wrap">
											<form action="<?php echo $bannerType ?>" method="POST" enctype="multipart/form-data">
												<h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>About Banner</h6>
												<hr class="light-grey-hr"/>
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label class="control-label mb-10">Title</label>
															<input type="text" name="title" class="form-control" placeholder="banner title" required="">
														</div>
													</div>
													<!--/span-->
													<div class="col-md-6">
														<div class="form-group">
															<label class="control-label mb-10">URL</label>
															<input type="text" name="url" class="form-control" placeholder="youtube.com/watch?v=123321" required="">
														</div>
													</div>
													<!--/span-->
												</div>
												<!--/row-->
												<div class="seprator-block"></div>
												<h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-collection-image mr-10"></i>upload image</h6>
												<hr class="light-grey-hr"/>
												<div class="row">
													<div class="col-lg-12">
														<div class="img-upload-wrap">
															<img class="img-responsive" src="<?php 
															if ( isset($_GET["b"]) AND $_GET["b"] != "main" )
																{
																	echo "../img/chair.jpg";
																}
																else
																{
																	echo "../img/slide1.jpg";
																}?>" alt="upload_img"> 
														</div>
														<div class="fileupload btn btn-info btn-anim"><i class="fa fa-upload"></i><span class="btn-text">Upload new image</span>
															<input type="file" name="logo" class="upload" required="">
														</div>
													</div>
												</div>
												<div class="seprator-block"></div>
												<div class="form-actions">
													<button class="btn btn-success btn-icon left-icon mr-10 pull-left"> <i class="fa fa-check"></i> <span>save</span></button>
													<a href="banners.php" ><button type="button" class="btn btn-warning pull-left">Return</button></a>
													<div class="clearfix"></div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>