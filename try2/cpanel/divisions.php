<?php
require("template/header.php");

if( isset($_GET["delId"]) && $delete = selectDB("types","`id` = '{$_GET["delId"]}'") ){
	updateDB("types",array("status"=>'1'),"`id` = '{$_GET["delId"]}'");
	header("LOCATION: divisions.php");die();
}

if( isset($_POST["title"]) && insertDB("types",array("title"=>"{$_POST["title"]}")) ){
	header("LOCATION: divisions.php");die();
}

?>

<div class="row" style="padding:16px">

<div class="row">
<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title txt-dark">Division Detail</h6>
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
<label class="control-label mb-10" for="">Division Title</label>
<div class="input-group">
<div class="input-group-addon"><i class="icon-text"></i></div>
<input type="text" class="form-control" id="" placeholder="Division Title" name="title" value="" required>
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
if ( $type = selectDB("types","`status` = '0'") ){
?>

<div class="row">
<div class="col-sm-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title txt-dark">Divisions</h6>
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
<th>Title</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php 
for( $i = 0 ; $i < sizeof($type) ; $i++ ){
	?>
<tr>
	<td class="txt-dark"><?php echo str_pad($i+1,2,"0",STR_PAD_LEFT) ?></td>
	
	<td class="txt-dark"><?php echo $type[$i]["title"] ?></td>
	
	<td>
	<a href="?delId=<?php echo $type[$i]["id"] ?>" class="font-18 txt-grey mr-10 pull-left" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></a>
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