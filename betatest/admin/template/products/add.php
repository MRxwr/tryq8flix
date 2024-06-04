<?php
include_once("includes/config.php");
?>
<div class="row">
<div class="col-sm-12">
<div class="panel panel-default card-view">
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="form-wrap">
<form action="includes/products/add.php" method="POST" enctype="multipart/form-data">
<h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>about product</h6>
<hr class="light-grey-hr"/>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">English Name</label>
<input type="text" name="enTitle" class="form-control" placeholder="Asus Monitor" required="">
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">English Sub text</label>
<input type="text" name="enSubText" class="form-control" placeholder='27", 360 rotation' for rest" required="">
</div>
</div>
<!--/span-->
</div>
<!-- Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">Arabic Name</label>
<input type="text" name="arTitle" class="form-control" placeholder="شاشة اسوس" required="">
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">Arabic Sub text</label>
<input type="text" name="arSubText" class="form-control" placeholder='27" مع قابلية الدوران الكامل' required="">
</div>
</div>
<!--/span-->
</div>
<!-- Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">Category</label>
<select name="categoryId" class="form-control" data-placeholder="Choose a Category" tabindex="1" required="">
<?php
$sql = "SELECT * FROM categories";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() )
{
?>
<option value="<?php echo $row["id"] ?>"><?php echo $row["enTitle"] ?></option>
<?php
}
?>
</select>
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">Brand</label>
<select name="brandId" class="form-control" data-placeholder="Choose a Brand" tabindex="1">
<option value="" >Choose a Brand</option>
<?php
$sql = "SELECT * FROM brand";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() )
{
?>
<option value="<?php echo $row["id"] ?>"><?php echo $row["enTitle"] ?></option>
<?php
}
?>
</select>
</div>
</div>
<!--/span-->
</div>
<!--/row-->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">Price</label>
<div class="input-group">
<div class="input-group-addon"><i class="ti-money"></i></div>
<input name="price" type="text" class="form-control" id="exampleInputuname" placeholder="153" required="">
</div>
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">Discount</label>
<div class="input-group">
<div class="input-group-addon"><i class="ti-cut"></i></div>
<input name="discount" type="text" class="form-control" id="exampleInputuname_1" placeholder="36%">
</div>
</div>
</div>
<!--/span-->
</div>
<div class="row">
<div class="col-sm-6">
<div class="form-group">
<label class="control-label mb-10">Video Link</label>
<input name="videoLink" type="text" class="form-control" placeholder="Video">
</div>
</div>
<div class="col-sm-6">
<div class="form-group">
<label class="control-label mb-10">Quantity</label>
<input name="quantity" type="text" class="form-control" placeholder="Quantity" required="">
</div>
</div>
</div>
<div class="seprator-block"></div>
<h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-comment-text mr-10"></i>English Details</h6>
<hr class="light-grey-hr"/>
<div class="row">
<div class="col-md-12">
<div class="form-group">
<textarea name="enDetails" class="tinymce" rows="4"></textarea>
</div>
</div>
</div>
<div class="seprator-block"></div>
<h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-comment-text mr-10"></i>Arabic Details</h6>
<hr class="light-grey-hr"/>
<div class="row">
<div class="col-md-12">
<div class="form-group">
<textarea name="arDetails" class="tinymce" rows="4"></textarea>
</div>
</div>
</div>
<!--/row-->
<div class="seprator-block"></div>
<h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-collection-image mr-10"></i>upload image</h6>
<hr class="light-grey-hr"/>
<div class="row">
<div class="col-lg-12">
<div class="img-upload-wrap">
<img class="img-responsive" src="../img/slide1.jpg" alt="upload_img"> 
</div>
<div class="fileupload btn btn-info btn-anim"><i class="fa fa-upload"></i><span class="btn-text">Upload new image</span>
<input name="logo[]" type="file" class="upload" required="" multiple="multiple">
</div>
</div>
</div>
<hr class="light-grey-hr"/>
<div class="form-actions">
<button class="btn btn-success btn-icon left-icon mr-10 pull-left"> <i class="fa fa-check"></i> <span>save</span></button>
<a href="product.php" ><button type="button" class="btn btn-warning pull-left">Return</button></a>
<div class="clearfix"></div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>