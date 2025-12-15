<style>
td{
	font-weight: 600;
}
</style>
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
	$color[] = $row["color"];
	$size[] = $row["size"];
	$length[] = $row["length"];
}

$sql = "SELECT *
		FROM `orders`
		WHERE `orderId` LIKE '$id'
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$voucher = $row["voucher"];
$storeLocation = $row["location"];
$notes = $row["notes"];
$discount = $row["discount"];
$country = $row["country"];
$area = $row["area"];
$block = $row["block"];
$street = $row["street"];
$house = $row["house"];
$avenue = $row["avenue"];
$notes = $row["notes"];
$building = $row["building"];
$floor = $row["floor"];
$apartment = $row["apartment"];
$areaA = $row["areaA"];
$blockA = $row["blockA"];
$streetA = $row["streetA"];
$avenueA = $row["avenueA"];
$notesA = $row["notesA"];
$discount = $row["discount"];
$totalPrice = $row["totalPrice"];
$totalPriceMain = $row["totalPrice"];
$place = $row["place"];
$charges = $row["d_s_charges"];
$creditTax = $row["creditTax"];
$postalCode = $row["postalCode"];
$method = $row["pMethod"];
if ( $method == 1 ){
	$method = "K-NET";
}elseif( $method == 2 ){
	$method = "Visa/Master";
}else{
	$method = "Cash";
}
?>
<div class="row">
<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="pull-left">

</div>
<div class="pull-right">

</div>
<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
<div class="panel-body">
<table style="width:100%">
<tr>
<td>
<span class="txt-dark head-font inline-block capitalize-font mb-5 rounded-circle text-center">
<img src="https://i.imgur.com/9emWZKM.png" style="width:150px; height:150px">
</span>
</td>
<td style="text-align: right;" class="txt-dark">
Order #<?php echo $row["orderId"]; ?>
</td>
</tr>
</table>
<table style="width:100%">
<tr>
<?php
if ( isset($row["location"]) ) {
?>
<td class="txt-dark">
<span class="txt-dark head-font inline-block capitalize-font mb-5">Address:</span>
<address class="mb-15" class="txt-dark">
<?php
if ( $place == "1" )
{
	echo $country . ", " . $area . "<br> قطعة " . $block . ", شارع " . $street ;
	if ( !empty($avenue) )
	{
		echo ", جادة " . $avenue;
	}

	echo ", منزل " . $house;
}
elseif($place == "2")
{
	echo $country . ", " . $areaA . "<br> قطعة " . $blockA . ", شارع " . $streetA;
	
	if ( !empty($avenueA) )
	{
		echo ", جادة " . $avenueA;
	}

	echo ", مبنى " . $building . ", طابق " . $floor . ", شقة " . $apartment;
}
else
{
    echo 'Pick up';
}
?>
</address>
</td>
<td style="text-align: right;">
<address>
<span class="txt-dark head-font capitalize-font mb-5">Date: <?php $row["date"] = explode(" ",$row["date"]); echo $row["date"][0] ?></span><br>
<span class="address-head mb-5">Phone: <?php echo $row["mobile"] ?></span>
</address>
</td>
</tr>
<tr>
<td colspan="2" style="
	width: 100%;
    text-align: right;
	"
	class="txt-dark">
<span class="address-head mb-5">Email: <?php echo $row["email"] ?></span>
</td>
</tr>
</table>
<?php
}
?>
<div class="invoice-bill-table">
<div class="table-responsive">
<table class="table table-hover" style="width:100%">
<thead>
<tr>
<th style="text-align: left;" class="txt-dark">Item</th>
<th style="text-align: left;" class="txt-dark">Price</th>
</tr>
</thead>
<tbody>
<?php

$sql = "SELECT
		`productId`, `quantity`, `size`
		FROM
		`orders`
		WHERE
		`orderId` LIKE '$id'";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() )
{
	$products[] = $row["productId"];
	$quantities[] = $row["quantity"];
	$size[] = $row["size"];
}
$totalPrice =0;
$i = 0;
while ( $i < sizeof($products) )
{
$sql = "SELECT
		p.* , o.productPrice, o.productDiscount
		FROM
		`products` as p
		JOIN
		`orders` as o
		ON
		p.id = o.productId
		WHERE
		p.id LIKE '".$products[$i]."'
		AND
		o.orderId LIKE '".$id."'
		";
$result = $dbconnect->query($sql);

while ( $row = $result->fetch_assoc() )
{
	if ( $row["productDiscount"] != 0 ){
		$checkPrice = $row["productPrice"] - ( $row["productPrice"] * $row["productDiscount"] / 100);
	}else{
		$checkPrice = $row["productPrice"];
	}
	$pricePerItem = $checkPrice*$quantities[$i];
	$totalPrice = $totalPrice + $pricePerItem;
	$productPrice = $checkPrice;

	?>
<tr>
<td class="txt-dark" style="white-space: break-spaces;"><?php echo $quantities[$i] ?> x <?php echo $row["enTitle"] . " [".$size[$i]."] "?></td>
<td class="txt-dark"><?php echo $productPrice ?> KD</td>
</tr>

<?php
}
$i++;
}
?>
<td style="margin-top:10px" ><br></td>
	<?php
	if ( isset($discount) AND $discount != '0' )
	{
		?>
	<tr class="txt-dark">
	<td>
	Discount
	</td>
	<td>
	<?php echo $discount ?>%
	</td>
	</tr>
	<?php
	}
	?>
	<!--<tr class="txt-dark">
	<td>Subtotal</td>
	<td><?php if ( isset($discount) ) { echo ($totalPrice - ( $totalPrice * $discount / 100)); } else {echo $totalPrice; } ?>KD</td>
	</tr>-->
	
	<?php
	if ( $creditTax != 0 )
	{
		?>
	<tr class="txt-dark">
	<td>
	Visa/Master Tax
	</td>
	<td>
	<?php echo $creditTax ?>KD
	</td>
	</tr>
	<?php
	}
	?>
	
	<?php
	$sql = "SELECT * FROM `vouchers` WHERE `id` LIKE '$voucher'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
if ( isset($row["voucher"]) AND !empty($row["voucher"]) ){
	?>
<tr class="txt-dark">
<td>Voucher</td>
<td><?php echo $row["voucher"]; ?></td>
</tr>
<?php
}
?>
<tr class="txt-dark">
<td>Delivery</td>
<td><?php echo $charges; ?>KD</td>
</tr>

<tr class="txt-dark">
<td>Payment method:</td>
<td><?php echo $method ?></td>
</tr>
	<?php
?>

<tr class="txt-dark">
<td>Total:</td>
<td><?php echo $totalPriceMain ?>KD</td>
</tr>
	<?php
?>
</tbody>
</table>
</div>
<div class="row">
<div class="col-xs-12">
<address>
<span class="txt-dark head-font capitalize-font mb-5">Special Instructions:</span>
<br>
<?php 
if ( $place == "1")
{
	echo $notes;
}
else
{
	echo $notesA;
}
?>
<br>
</address>
</div>
</div>
<table style="width:100%;position: absolute;
    bottom: 0px;">
<tr class="row text-center" style="display:flex;justify-content: space-between;bottom:0px;">

    <td class="col" style="text-align:center">
        <img src="https://i.imgur.com/dEBbuF6.png" style="width:10px;height:10px"> +96599696665
    </td>
    <td class="col" style="text-align:center">
        <img src="https://i.imgur.com/azY1RCs.png" style="width:10px;height:10px"> Rich_kw
    </td>
    <td class="col" style="text-align:center">
        <img src="https://i.imgur.com/1sMHvsQ.png" style="width:10px;height:10px"> Rich_kuw
    </td>

</tr>
</table>

<div class="button-list pull-right">
<!--<button type="submit" class="btn btn-success mr-10">
Proceed to payment 
</button>-->
<button type="button" class="btn btn-primary btn-outline btn-icon left-icon takeMeToPrinter"> 
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