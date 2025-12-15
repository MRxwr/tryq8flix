<?php
require ("includes/config.php");

if ( isset($_GET["status"]) )
{
	$id = $_GET["id"];
	if ( $_GET["status"] == "failed" )
	{
		$sql = "UPDATE `orders` SET `status`='2' WHERE `orderId` LIKE '".$id."'";
		$result = $dbconnect->query($sql);
	}
	elseif ( $_GET["status"] == "success")
	{
		$sql = "UPDATE `orders` SET `status`='1' WHERE `orderId` LIKE '".$id."'";
		$result = $dbconnect->query($sql);
	}
	elseif ( $_GET["status"] == "pending")
	{
		$sql = "UPDATE `orders` SET `status`='0' WHERE `orderId` LIKE '".$id."'";
		$result = $dbconnect->query($sql);
	}
	
}

$sql = "SELECT o.*, c.fullName
		FROM `orders` as o
		JOIN `customers` as c ON o.customerId = c.id
		GROUP BY `orderId`

		UNION

		SELECT o.*, u.fullName
		FROM `orders` as o
		JOIN `users` as u ON o.userId = u.id
		GROUP BY `orderId`
		ORDER BY `date` DESC
		";
$result = $dbconnect->query($sql);

?>
<div class="row">
<div class="col-sm-12">
<div class="panel panel-default card-view">
<div class="panel-wrapper collapse in">
<div class="panel-body row">
<div class="table-wrap">
<div class="table-responsive">
<table class="table display responsive product-overview mb-30" id="myTable">
<thead>
<tr>
<th>Date / Time</th>
<th>Name</th>
<th>Order ID</th>
<th>Client</th>
<th>Price</th>
<th>Method</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php 
while ( $row = $result->fetch_assoc() )
{
$orederID = $row["orderId"];
?>
<tr>
<td><?php echo $row["date"] ?></td>
<td class="txt-dark"><?php echo $row["fullName"] ?></td>
<td class="txt-dark"><?php echo $row["orderId"] ?></td>
<td><?php if ( $row["userId"] == 0 ) { echo "Customer"; } else { echo "User"; } ?></td>
<td><?php echo $row["totalPrice"] ?></td>
<td><?php if ( $row["pMethod"] == 1 ) {echo "<b style='color:darkgreen'>Cash</b>"; } else { echo "<b style='color:darkblue'>K-Net</b>";} ?></td>
<td>
<?php 
if ( $row["status"] == 2 )
{
echo "<span class='label label-default font-weight-100'>Failed</span>";
}
elseif ( $row["status"] == 1 )
{
echo "<span class='label label-success font-weight-100'>Paid</span>";
}
elseif ( $row["status"] == 0 )
{
echo "<span class='label label-warning font-weight-100'>Pending</span>";
}
?>
</td>
<td>
<a href="?info=view&id=<?php echo $orederID ?>"><button class="btn  btn-primary btn-rounded">Details</button></a>
<a href="?status=failed&id=<?php echo $orederID ?>"><button class="btn btn-danger btn-icon-anim btn-circle"><i class="icon-trash"></i></button></a>
<a href="?status=pending&id=<?php echo $orederID ?>"><button class="btn btn-warning btn-icon-anim btn-circle"><i class="icon-settings"></i></button></a>
<a href="?status=success&id=<?php echo $orederID ?>"><button class="btn btn-success btn-icon-anim btn-circle"><i class="icon-check"></i></button></a>
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