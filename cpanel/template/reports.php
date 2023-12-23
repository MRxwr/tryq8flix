<style>
[type="date"] {
  background:#fff url(https://cdn1.iconfinder.com/data/icons/cc_mono_icon_set/blacks/16x16/calendar_2.png)  97% 50% no-repeat ;
}
[type="date"]::-webkit-inner-spin-button {
  display: none;
}
[type="date"]::-webkit-calendar-picker-indicator {
  opacity: 0;
}
label {
  display: block;
}
input {
  border: 1px solid #c4c4c4;
  border-radius: 5px;
  background-color: #fff;
  padding: 3px 5px;
  box-shadow: inset 0 3px 6px rgba(0,0,0,0.1);
  width: 100%;
}
</style>
<div class="row">
<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="pull-left">
</div>
</div>
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="form-wrap mt-10">
<form action="" method="POST">
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10 text-left">Start Date</label>
<input type="date" name="startDate" required >
</div>
</div>		
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10 text-left">End Date</label>
<input type="date" name="endDate" required >
</div>
</div>	
</div>
<div class="row">
<!--
<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10">Select Product</label>
<select class="selectpicker productId" name="productId" data-style="form-control btn-default btn-outline">
<option></option>
<?php
require("includes/config.php");
$dhlBills = 0;
$sql = "SELECT * 
		FROM 
		`products`
		WHERE `hidden` != 2";
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

<div class="col-md-4">
<div class="form-group sizes">
<label class="control-label mb-10"><?php echo $selectSubProduct ?></label>
<select class="selectpicker" name="" data-style="form-control btn-default btn-outline">
<option></option>
</select>
</div>	
</div>

<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10">Select Voucher</label>
<select class="selectpicker" name="voucher" data-style="form-control btn-default btn-outline">
<option></option>
<?php
$sql = "SELECT *
		FROM `vouchers`
		WHERE
		`hidden` = '0'
		GROUP BY `voucher`
		";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() )
{
?>
<option value="<?php echo $row["id"] ?>"><?php echo $row["voucher"] ?></option>
<?php
}
?>
?>
</select>
</div>	
</div>
-->
<div class="col-md-12">
<div class="form-group">
<button class="btn  btn-success">Submit</button>
</div>
</div>
<?php
if ( isset($_POST["endDate"]) )
{
	$sql = "SELECT
			(totalPrice - creditTax) as realTotal, `quantity`, `orderId`, `status`, `country`, `d_s_charges`
			FROM
			`orders` 
			WHERE 
			`Date` BETWEEN '".$_POST['startDate']."' AND '".$_POST["endDate"]."'
			AND
			`status` != 0
			AND
			`status` != 2020
			AND
			`status` != 2
			";
			if ( isset($_POST["voucher"]) AND !empty($_POST["voucher"]) ){
				$sql .= " AND `voucher` LIKE '%" .$_POST["voucher"]. "%'";
			}
			if ( isset($_POST["productId"]) AND !empty($_POST["productId"]) ){
				$sql .= " AND `productId` LIKE '" .$_POST["productId"]. "'";
			}
			if ( isset($_POST["size"]) AND !empty($_POST["size"]) ){
				$sql .= " AND `size` LIKE '" .$_POST["size"]. "'";
			}
			if ( isset($_POST["pmethod"]) AND !empty($_POST["pmethod"]) ){
				$sql .= " AND `Payment` LIKE '%" .$_POST["pmethod"]. "%'";
			}
			if ( isset($_POST["employeeId"]) AND !empty($_POST["employeeId"]) ){
				$sql .= " AND `eName` LIKE '%" .$_POST["employeeId"]. "%'";
			}
	$sql .= " GROUP BY `orderId`";
	$result = $dbconnect->query($sql);
	
	//
	$totalFromProduct = 0;
	while ( $row = $result->fetch_assoc() )
	{
		if ( $row["status"] != "0" )
		{
			if ( isset($_POST["productId"]) AND !empty($_POST["productId"]) ){
				$totalFromProduct = $totalFromProduct + $row["quantity"];
			}
			$orderIds[] = $row["orderId"];
			$totals[] = $row["realTotal"];
			if ( $row["country"] != "KW" ){
				$shippingCharges[] = $row["d_s_charges"];
				$deliveryCharges[] = 0;
			}else{
				$deliveryCharges[] = $row["d_s_charges"];
				$shippingCharges[] = 0;
			}
			if ( $row["country"] != "KW" ){
				$dhlBills++;
			}
		}
	}
	
	$i = 0;
	if ( !empty($orderIds[0]) )
	{
		while ( $i < sizeof($orderIds) )
		{
			$sql = "SELECT
					`productId`, `quantity`, `size`
					FROM
					`orders` 
					WHERE
					`orderId` = '".$orderIds[$i]."'
					";
			$result = $dbconnect->query($sql);
			while ( $row = $result->fetch_assoc() )
			{
				$productIds[] = $row["productId"];
				$quantities[] = $row["quantity"];
				$size[] = $row["size"];
			}
			$i++;
		}
		
		$i = 0;
		while ( $i < sizeof($productIds) )
		{
			$sql = "SELECT
					`cost`,`price`
					FROM
					`subproducts` 
					WHERE
					`productId` = '".$productIds[$i]."'
					AND
					`size` LIKE '".$size[$i]."'
					AND
					`hidden` = '0'
					";
			$result = $dbconnect->query($sql);
			while ( $row = $result->fetch_assoc() )
			{
				$cost[] = $row["cost"] * $quantities[$i];
				$price[] = $row["price"] * $quantities[$i];
			}
			$i++;
		}
	}
}
?>
</div>
</form>
</div>
</div>
</div>
</div>

<!--
<div class="row">
<div class="col-sm-12">
<div class="panel panel-default card-view">
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="table-wrap">
<div class="table-responsive">
<button class="btn btn-primary printMeNow">Print</button>
<div class="printable">
<table class="table table-hover display pb-30" >
<thead>
<tr>
<th><?php echo "Earned" ?></th>
<th><?php echo "Delivery" ?></th>
<th><?php echo "Shipping" ?></th>
<th><?php echo "Cost" ?></th>
<th><?php echo "Profit" ?></th>
<th><?php echo "Quantity" ?></th>
<th><?php echo "International Bills" ?></th>
</tr>
</thead>
<tbody>
<tr>
<td><?php if ( isset($totals) ) {echo (float)round(array_sum($totals),2); } ?>KD</td>
<td><?php if ( isset($deliveryCharges) ){ echo array_sum($deliveryCharges);}else{ echo 0 ;}; ?>KD</td>
<td><?php if ( isset($shippingCharges) ){ echo array_sum($shippingCharges);}else{ echo 0 ;}; ?>KD</td>
<td><?php if ( isset($cost) ) {echo array_sum($cost);} ?>KD</td>
<td><?php if ( isset($cost) ) {echo (float)round(array_sum($totals) - array_sum($cost) - array_sum($shippingCharges) - array_sum($deliveryCharges),2);} ?>KD</td>
<td><?php
if ( isset($_POST["productId"]) AND !empty($_POST["productId"]) ){
	echo $totalFromProduct;
}else{
	echo 0;
}
?></td>
<td><?php echo $dhlBills; ?></td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>	
</div>
</div>
-->

<div class="row">
<div class="col-sm-12">
<div class="panel panel-default card-view">
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="table-wrap">
<div class="table-responsive">
<table id="example" class="table table-hover display  pb-30" >
<thead>
<tr>
<th><?php echo $DateTime ?></th>
<th><?php echo $OrderID ?></th>
<th><?php echo $Mobile ?></th>
<th><?php echo $Voucher ?></th>
<th><?php echo $Discount ?></th>
<th><?php echo $deliveryText ?></th>
<th><?php echo $paymentMethodText ?></th>
<th><?php if ( $directionHTML == "rtl" ){echo "الأرباح";}else{echo "Profit";} ?></th>
<th><?php echo $Cost ?></th>
<th><?php echo $Price ?></th>
</tr>
</thead>
<tbody>
<?php
$i = 0 ;
if ( isset($orderIds) )
{
while ( $i < sizeof($orderIds) )
{
	$sql = "SELECT
			o.*, SUM(sp.cost) as totalCost, (totalPrice - SUM(sp.cost) - d_s_charges - creditTax) as totalProfit
			FROM `orders` as o
			JOIN `subproducts` as sp
			ON o.productId = sp.productId AND o.size = sp.size
			WHERE
			`orderId` = '".$orderIds[$i]."'
			AND
			sp.hidden = 0
			GROUP BY `orderId`
			";
	$result = $dbconnect->query($sql);
	while ( $row = $result->fetch_assoc() )
	{
?>
<tr>
<td><?php echo $row["date"] ?></td>
<td class="txt-dark"><a href="product-orders.php?info=view&id=<?php echo $row["orderId"] ?>" target="_blank"><?php echo $row["orderId"] ?></a></td>
<td class="txt-dark"><?php echo $row["mobile"] ?></td>
<td><?php echo $row["voucher"] ?></td>
<td><?php echo $row["discount"] ?></td>
<td><?php echo $row["d_s_charges"] ?></td>
<td><?php if ( $row["pMethod"] == 1 ) { echo "K-NET"; } else { echo "Visa/Master";} ?></td>
<td><?php echo (float)round($row["totalProfit"],2) ?></td>
<td><?php echo $row["totalCost"] ?></td>
<td><?php echo $row["totalPrice"] ?></td>
</tr>
<?php
	}
	$i++;
}
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
<!-- /Row -->
</div>
