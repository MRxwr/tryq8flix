<?php
require ("includes/config.php");
$id = $_GET["id"];

$sql = "SELECT *
		FROM `orders`
		WHERE `orderId` LIKE '$id'
		";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() )
{
	$productIds[] = $row["productId"];
	$quantity[] = $row["quantity"];
	$totalPrice = $row["totalPrice"];
	$userId = $row["userId"];
	$customerId = $row["customerId"];
	$addressId = $row["addressId"];
	$orderDate = $row["date"];
	$pMethod = $row["pMethod"];
}
if ( $userId != 0 )
{
	$sql = "SELECT u.*, l.*  
				FROM `users` AS u
				JOIN `locations` AS l
				ON u.id = l.userId
				WHERE u.id LIKE $userId AND l.id LIKE $addressId
				";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$fullName = $row["fullName"];
	$email = $row["email"];
	$phone = $row["phone"];
	$country = $row["country"];
	$city = $row["city"];
	$block = $row["block"];
	$street = $row["street"];
	$house = $row["house"];
	$zipcode = $row["zipcode"];
}
else
{
	$sql = "SELECT c.*, l.*  
			FROM `customers` AS c
			JOIN `locations` AS l
			ON c.id = l.customerId
			WHERE c.id LIKE $customerId AND l.id LIKE $addressId
		";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$fullName = $row["fullName"];
	$email = $row["email"];
	$phone = $row["phone"];
	$country = $row["country"];
	$city = $row["city"];
	$block = $row["block"];
	$street = $row["street"];
	$house = $row["house"];
	$zipcode = $row["zipcode"];
}
?>
<div class="row">
<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title txt-dark">Invoice</h6>
</div>
<div class="pull-right">
<h6 class="txt-dark">Order # <?php echo $id ?></h6>
</div>
<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="row">
<div class="col-xs-6">
<span class="txt-dark head-font inline-block capitalize-font mb-5">Billed to:</span>
<address class="mb-15">
<span class="address-head mb-5"><?php echo $fullName ?></span>
</address>
</div>
<div class="col-xs-6 text-right">
<span class="txt-dark head-font inline-block capitalize-font mb-5">shiped to:</span>
<address class="mb-15">
<?php echo "Country: " . $country . "<br>" ?>
<?php echo "City: " . $city . "<br>" ?>
<?php echo "Block: " . $block . "<br>" ?>
<?php echo "Street: " . $street . "<br>" ?>
<?php echo "House: " . $house . "<br>" ?><br>
</address>
</div>
</div>

<div class="row">
<div class="col-xs-6">
<address>
<span class="txt-dark head-font capitalize-font mb-5">Contact info:</span>
<br>
<abbr title="Phone">Phone: </abbr><?php echo $phone ?><br>
<?php echo $email ?>
</address>
</div>
<div class="col-xs-6 text-right">
<address>
<span class="txt-dark head-font capitalize-font mb-5">order date:</span><br>
<?php $orderDate = explode(" ",$orderDate); echo $orderDate[0] ?><br><br>
</address>
</div>
</div>
<div class="seprator-block"></div>
<div class="invoice-bill-table">
<div class="table-responsive">
<table class="table table-hover">
<thead>
<tr>
<th>Item</th>
<th>Price</th>
<th>Discount</th>
<th>Quantity</th>
<th>Totals</th>
</tr>
</thead>
<tbody>
<?php
$subTotal = array(); 
$i = 0;
$buildPC = array();
$buildPC = [
	"6" => 0,
	"7" => 0,
	"8" => 0,
	"9" => 0,
	"10" => 0,
	"11" => 0,
	"19" => 0,
	"5" => 0
];
$keys = array_keys($buildPC);
while ( $i < sizeof($quantity) )
{
	$sql = "SELECT *
	FROM `products`
	WHERE `id` LIKE '$productIds[$i]'
	";
	$result = $dbconnect->query($sql);
	while ( $row = $result->fetch_assoc() )
	{
		if ( $row["categoryId"] == 17 )
		{
			$serviceCharges = 0;
		}

		if ( in_array($row["categoryId"],$keys) )
		{
			$buildPC[$keys[$i]] = 1;
		}
		if ( in_array("0",$buildPC) )
		{
		}
		else
		{
			$serviceCharges = 0;
		}

		?>
		<tr>
		<td><?php echo $row["enTitle"] ?></td>
		<td><?php echo $row["price"] ?> KD</td>
		<td><?php echo $row["discount"] . "%"; ?></td>
		<td><?php echo $quantity[$i] ?></td>
		<td><?php
		if ( !empty($row["discount"]) ) 
		{
			echo $subTotal[] = ($row["price"]-($row["price"]*$row["discount"]/100))*$quantity[$i];
		}
		else
		{
			echo $subTotal[] = $quantity[$i]*$row["price"];
		} 
		?> KD </td>
		</tr>
		<?php
	}
	$i++;
}
?>
<?php
if ( 
	(
		( array_sum($subTotal) != $totalPrice )
		AND 
		( isset($serviceCharges) AND $serviceCharges != 0 )
	) 
	OR
	(
		( array_sum($subTotal) != $totalPrice )
		AND 
		( !isset($serviceCharges) )
	)
	)
{
	?>
	<tr class="txt-dark">
	<td></td>
	<td></td>
	<td></td>
	<td>Shipping Charges</td>
	<td>3 KD</td>
	</tr>
	<?php
}
?>
<tr class="txt-dark">
<td></td>
<td></td>
<td></td>
<td>Total</td>
<td><?php echo $totalPrice ?> KD</td>
</tr>
<tr class="txt-dark">
<td></td>
<td></td>
<td></td>
<td>Payment Method</td>
<td><?php if ( $pMethod == 0 ) { echo "K-Net"; } else { echo "Cash"; } ?></td>
</tr>
</tbody>
</table>
</div>
<div class="button-list pull-right">
<button type="button" class="btn btn-primary btn-outline btn-icon left-icon" onclick="javascript:window.print();"> 
<i class="fa fa-print"></i><span> Print</span> 
</button>
</div>
<div class="clearfix"></div>
</div>
</div>
</div>
</div>
</div>
</div>