<?php
require ("includes/config.php");
$sql= "	SELECT p.*, i.imageurl
		FROM `products` AS p 
		JOIN `images` AS i 
		ON p.id = i.productId
		GROUP BY i.productId	
		";
$result = $dbconnect->query($sql);
?>
<div class="row">
<?php
while ( $row = $result->fetch_assoc() )
{
?>
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6">
		<div class="panel panel-default card-view pa-0">
			<div class="panel-wrapper collapse in">
				<div class="panel-body pa-0">
					<article class="col-item">
						<div class="photo">
							<div class="options">
								<a href="add-products.php?act=edit&id=<?php echo $row["id"] ?>" class="font-18 txt-grey mr-10 pull-left"><i class="zmdi zmdi-edit"></i></a>
								<a href="includes/products/delete.php?id=<?php echo $row["id"] ?>" class="font-18 txt-grey pull-left"><i class="zmdi zmdi-close"></i></a>
								</div>
								<img src="../logos/<?php echo $row["imageurl"] ?>" style="width:100%;height:150px" alt="Product Image" />
							</div>
							<div class="info" style="height: 150px; overflow:hidden;">
								<h6><?php echo $row["enTitle"] ?></h6>
								<div class="product-rating inline-block">
									<a href="javascript:void(0);" class="font-12 txt-success zmdi zmdi-star mr-0"></a>
									<a href="javascript:void(0);" class="font-12 txt-success zmdi zmdi-star mr-0"></a>
									<a href="javascript:void(0);" class="font-12 txt-success zmdi zmdi-star mr-0"></a>
									<a href="javascript:void(0);" class="font-12 txt-success zmdi zmdi-star mr-0"></a>
									<a href="javascript:void(0);" class="font-12 txt-success zmdi zmdi-star-outline mr-0">
									</a>
								</div>
								<span class="head-font block text-warning font-16"><?php echo $row["price"] . " KWD" ?></span>
							</div>
					</article>
				</div>
			</div>	
		</div>	
	</div>	
<?php
}
?>					
</div>	
