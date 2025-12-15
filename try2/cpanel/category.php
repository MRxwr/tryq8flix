<?php
require("template/header.php");

if( isset($_GET["delId"]) && $delete = selectDB("category","`id` = '{$_GET["delId"]}'") ){
	updateDB("category",array("status"=>'1'),"`id` = '{$_GET["delId"]}'");
	header("LOCATION: category.php");die();
}

if( isset($_POST["title"]) ){
	unset($_POST["submtit"]);
	$_POST["details"] = urlencode($_POST["details"]);
	insertDB("category",$_POST);
	header("LOCATION: category.php");die();
}

?>

<div class="row" style="padding:16px">

<div class="row">
<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title txt-dark">Categories Detail</h6>
</div>
<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="row">
<div class="col-sm-12 col-xs-12">
<div class="form-wrap">

<form action="" method="post" enctype="multipart/form-data">

<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10" for="">type</label>
<div class="input-group">
<div class="input-group-addon"><i class="icon-text"></i></div>
<select class="form-control" name="type" required>
	<?php
	$types = selectDB("types","`status` = '0'");
	for( $i = 0; $i<sizeof($types); $i++){
		echo "<option value='{$types[$i]["id"]}'>{$types[$i]["title"]}</option>";
	}
	?>
</select>
</div>
</div>
</div>

<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10" for="">Title</label>
<div class="input-group">
<div class="input-group-addon"><i class="icon-text"></i></div>
<input type="text" class="form-control" id="" placeholder="Title" name="title" value="" required>
</div>
</div>
</div>

<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10" for="">Details</label>
<div class="input-group">
<div class="input-group-addon"><i class="icon-text"></i></div>
<input type="text" class="form-control" id="" placeholder="details" name="details" value="" required>
</div>
</div>
</div>

<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10" for="">Trailer</label>
<div class="input-group">
<div class="input-group-addon"><i class="icon-text"></i></div>
<input type="text" class="form-control" id="" placeholder="Youtube" name="trailer" value="" required>
</div>
</div>
</div>

<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10" for="">IMDb</label>
<div class="input-group">
<div class="input-group-addon"><i class="icon-text"></i></div>
<input type="text" class="form-control" id="" placeholder="IMDb Rating" name="imdb" value="" required>
</div>
</div>
</div>

<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10" for="">Year</label>
<div class="input-group">
<div class="input-group-addon"><i class="icon-text"></i></div>
<input type="text" class="form-control" id="" placeholder="Year" name="year" value="" required>
</div>
</div>
</div>

<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10" for="">Duration</label>
<div class="input-group">
<div class="input-group-addon"><i class="icon-text"></i></div>
<input type="text" class="form-control" id="" placeholder="Running time in min" name="duration" value="" required>
</div>
</div>
</div>

<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10" for="">Poster</label>
<div class="input-group">
<div class="input-group-addon"><i class="icon-text"></i></div>
<input type="file" class="form-control" id="" placeholder="" name="poster">
</div>
</div>
</div>

<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10" for="">Header</label>
<div class="input-group">
<div class="input-group-addon"><i class="icon-text"></i></div>
<input type="file" class="form-control" id="" placeholder="" name="header">
</div>
</div>
</div>	

<div class="col-md-6">
<button type="submit" class="btn btn-success" style="margin-top:30px">Submit</button>
</div>

</form>

</div>
</div>
</div>
</div>
</div>
</div>
</div>

</div>

<?php
if ( $category = selectDB("category","`status` = '0'") ){
?>

<div class="row">
<div class="col-sm-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title txt-dark">Categories</h6>
</div>
<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="table-wrap">
<div class="">

<table id="myTable1" class="table table-hover display pb-30">
<thead>
<tr>
<th>#</th>
<th>Type</th>
<th>Title</th>
<th>IMDBb</th>
<th>trailer</th>
<th>year</th>
<th>Duration</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php 
for( $i = 0 ; $i < sizeof($category) ; $i++ ){
	$catType = selectDB("types","`id` = '{$category[$i]["type"]}'");
	?>
<tr>
	<td class="txt-dark"><?php echo str_pad($i+1,2,"0",STR_PAD_LEFT) ?></td>
	
	<td class="txt-dark"><?php echo $catType[0]["title"] ?></td>
	<td class="txt-dark"><?php echo $category[$i]["title"] ?></td>
	<td class="txt-dark"><?php echo $category[$i]["imdb"] ?></td>
	<td class="txt-dark"><?php echo $category[$i]["trailer"] ?></td>
	<td class="txt-dark"><?php echo $category[$i]["year"] ?></td>
	<td class="txt-dark"><?php echo $category[$i]["duration"] ?></td>
	
	<td>
	<a href="?delId=<?php echo $category[$i]["id"] ?>" class="font-18 txt-grey mr-10 pull-left" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></a>
	</td>
</tr>
<?php
}
?>
</tbody>
</table>
</div>
</div>	
</div>	
</div>
</div>
</div>
</div>

<?php
}
?>
</div>
<!-- /Row -->
<?php require("template/footer.php") ?>