<?php
require ("includes/config.php");
$id = $_GET["id"];
$sql = "SELECT * FROM `products` WHERE `id` = $id";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$arTitle = $row["arTitle"];
$enTitle = $row["enTitle"];
$arDetails = $row["arDetails"];
$enDetails = $row["enDetails"];
$enSubText = $row["enSubText"];
$arSubText = $row["arSubText"];
$categoryId = $row["categoryId"];
$brandId = $row["brandId"];
$price = $row["price"];
$discount = $row["discount"];
$videoLink = $row["video"];
$quantity = $row["quantity"];
$size = "";
$color = "";
$rating = "";
?>
<div class="row">
<div class="col-sm-12">
<div class="panel panel-default card-view">
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="form-wrap">
<form action="includes/products/edit.php?id=<?php echo $id ?>" method="POST" enctype="multipart/form-data">
<h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>about product</h6>
<hr class="light-grey-hr"/>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">English Name</label>
<input type="text" name="enTitle" class="form-control" value="<?php echo $enTitle ?>">
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">English Sub text</label>
<input type="text" name="enSubText" class="form-control" value="<?php echo $enSubText ?>">
</div>
</div>
<!--/span-->
</div>
<!-- Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">Arabic Name</label>
<input type="text" name="arTitle" class="form-control" value="<?php echo $arTitle ?>">
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">Arabic Sub text</label>
<input type="text" name="arSubText" class="form-control" value="<?php echo $arSubText ?>">
</div>
</div>
<!--/span-->
</div>
<!-- Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">Category</label>
<select name="categoryId" class="form-control" data-placeholder="Choose a Category" tabindex="1">
<?php
$sql = "SELECT * FROM categories WHERE id LIKE $categoryId";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
?>
<option value="<?php echo $row["id"] ?>"><?php echo $row["enTitle"] ?></option>
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
<?php
$sql = "SELECT * FROM brand WHERE id LIKE $brandId";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
?>
<option value="<?php echo $row["id"] ?>"><?php echo $row["enTitle"] ?></option>
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
<input name="price" type="text" class="form-control" id="exampleInputuname" value="<?php echo $price ?>">
</div>
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10">Discount</label>
<div class="input-group">
<div class="input-group-addon"><i class="ti-cut"></i></div>
<input name="discount" type="text" class="form-control" id="exampleInputuname_1" value="<?php echo $discount ?>">
</div>
</div>
</div>
<!--/span-->
</div>
<div class="row">
<div class="col-sm-6">
<div class="form-group">
<label class="control-label mb-10">Video Link</label>

<input name="videoLink" type="text" class="form-control" value="<?php echo $videoLink ?>">
</div>
</div>
<div class="col-sm-6">
<div class="form-group">
<label class="control-label mb-10">Quantity</label>

<input name="quantity" type="text" class="form-control" value="<?php echo $quantity ?>">
</div>
</div>
</div>
<div class="seprator-block"></div>
<h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-comment-text mr-10"></i>English Details</h6>
<hr class="light-grey-hr"/>
<div class="row">
<div class="col-md-12">
<div class="form-group">
<textarea name="enDetails" class="tinymce" rows="4"><?php echo $enDetails ?></textarea>
</div>
</div>
</div>
<div class="seprator-block"></div>
<h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-comment-text mr-10"></i>Arabic Details</h6>
<hr class="light-grey-hr"/>
<div class="row">
<div class="col-md-12">
<div class="form-group">
<textarea name="arDetails" class="tinymce" rows="4"><?php echo $arDetails ?></textarea>
</div>
</div>
</div>
<!--/row-->
<div class="seprator-block"></div>
<h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-collection-image mr-10"></i>upload image</h6>
<hr class="light-grey-hr"/>
<div class="row">
<div class="col-lg-12">
<?php 
$sql = "SELECT * FROM `images` WHERE `productId` = $id";
$result = $dbconnect->query($sql);
if ( $result->num_rows > 0 )
{
while ( $row = $result->fetch_assoc() )
{
?>
<div class="img-upload-wrap">
<table style="width:100%"><tr><td style="width:300px"><img class="img-responsive" style="width:300px;height:300px" src="../logos/<?php echo $row["imageurl"];?>" alt="upload_img"></td></tr><tr><td class="btn btn-info btn-icon left-icon"><a href="<?php echo "add-products.php?act=". $_GET["act"] ."&id=". $id ."&imgdel" . "=" .$row["id"] ?>" target="" style="text-decoration:none;color:white">Delete</a></td></tr></table>
</div>
<?php
}
}
else
{
?>
<div class="img-upload-wrap">
<img class="img-responsive" src="../img/slide1.jpg" alt="upload_img"> 
</div>
<?php
}
?>
<div style="padding-top:10px"></div>
<div class="fileupload btn btn-info btn-anim"><i class="fa fa-upload"></i><span class="btn-text">Upload new image</span>
<input type="file" name="logo[]"
multiple="multiple" 
class="upload">
</div>
</div>
</div>
<hr class="light-grey-hr"/>

<div class="form-actions">
<button class="btn btn-success btn-icon left-icon mr-10 pull-left"> <i class="fa fa-check"></i> <span>save</span></button>
<a href="product.php"><button type="button" class="btn btn-warning pull-left">Return</button></a>
<div class="clearfix"></div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>