<style>
body{background-color:#fafafa}

</style>
<?php
date_default_timezone_set('Asia/Riyadh');
$check = ["'",'"',")","(",";","?",">","<","~","!","#","$","%","^","&","*","-","_","=","+","/","|",":"];

if ( !isset($_GET["paymentId"]) )
{
    header("LOCATION: checkout.php?status=fail");
    die();
}
else
{
	$token = "cxu2LdP0p0j5BGna0velN9DmzKJTrx3Ftc0ptV8FmvOgoDqvXivkxZ_oqbi_XM9k7jgl3SUriQyRE2uaLWdRumxDLKTn1iNglbQLrZyOkmkD6cjtpAsk1_ctrea_MeOQCMavsQEJ4EZHnP4HoRDOTVRGvYQueYZZvVjsaOLOubLkdovx6STu9imI1zf5OvuC9rB8p0PNIR90rQ0-ILLYbaDZBoQANGND10HdF7zM4qnYFF1wfZ_HgQipC5A7jdrzOoIoFBTCyMz4ZuPPPyXtb30IfNp47LucQKUfF1ySU7Wy_df0O73LVnyV8mpkzzonCJHSYPaum9HzbvY5pvCZxPYw39WGo8pOMPUgEugtaqepILwtGKbIJR3_T5Iimm_oyOoOJFOtTukb_-jGMTLMZWB3vpRI3C08itm7ealISVZb7M3OMPPXgcss9_gFvwYND0Q3zJRPmDASg5NxRlEDHWRnlwNKqcd6nW4JJddffaX8p-ezWB8qAlimoKTTBJCe5CnjT4vNjnWlJWscvk38VNIIslv4gYpC09OLWn4rDNeoUaGXi5kONdEQ0vQcRjENOPAavP7HXtW1-Vz83jMlU3lDOoZsdEKZReNYpvdFrGJ5c3aJB18eLiPX6mI4zxjHCZH25ixDCHzo-nmgs_VTrOL7Zz6K7w6fuu_eBK9P0BDr2fpS"; 
	
	//$token = "hE-2B3TuAQ-ea717-mLkkfajc240Eh4PmRFLRugNAw3aQMTfsNaL9_IsHPKEYQ7P7Ov7AyXRk_JRTOEOP9aNt6KbOx5bWU7P60vqFEHyMSqGXMyTyFzR7knj9eJukd-fr2VKK0Ti0Xic2z7dmYeZAQ8gZd_LOmDHy8kMqBaL6Sgom0HRGJxNXy8dIqcyVe2vgJ5fjE40NzrTKktY9E5_3ELgTi5qFgAZTDk76WmblxT36oCZqAs2BxhBVD2-3uQbrEN3FtdQ8sladuRF5CX4znVQ7eSXZ1UyzcDiW2FqyNVbU2JasG9MC2u8Cz5xLKO1dU8PDXaETqeDJ-8DLxQ-1fed7NqJKSPnGOMwSrSRDIzCqRtqeXVVaqgngy0GDM88NRusZmBq73zqao577UfZcGjNGo-hlbPYS_0gYm-fAla0OkZeZjAJCgrDNTu0L1As0P27crSu2LUl6MNZn5iHkd1lUiCnRPwE7Ppky1C_t-l6lCuQcv-hkV9fv-EbcsIdnhBZhzG7_QG9jEZVjpj_FxcSTlv0EraCdI9O4rd0-pYesfbEEAE6YseARJ4iRXXVOYzy_lMLqGfu1kw_bOjJp1VPCMJA78N6uIh9PFdozgfBq6-UkDTCOEnozsRsILfO96buzhRRF0Czkde4NvBzt7jAPoqbEFcOn4mwzkLa_qDPOoVMOsQc12Vgcsb7klV0ktRJBA"; 
	//token value to be placed here;
	$basURL = "https://apitest.myfatoorah.com";

	$invoiceArray = 
	[
		"Key" => $_GET["paymentId"],
		"KeyType" => 'paymentId'
	];
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "$basURL/v2/GetPaymentStatus",
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => json_encode($invoiceArray),
	  CURLOPT_HTTPHEADER => array("Authorization: Bearer $token","Content-Type: application/json"),
	));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);
	if ($err) 
	{
		echo "cURL Error #:" . $err;
	} 
	else 
	{
		$resultMY = json_decode($response, true);
		$orderId = $resultMY["Data"]["InvoiceId"];
	}

    $sql = "UPDATE 
            `orders`
            SET 
            `status` = '1'
            WHERE 
            `orderId` = '".$orderId."'
            ";
    $result = $dbconnect->query($sql);
    
    $sql = "SELECT `productId`, `quantity`, `color`, `size`
            FROM `orders`
            WHERE `orderId` = '".$orderId."'
            ";
    $result = $dbconnect->query($sql);
    while ( $row = $result->fetch_assoc() )
    {
        $quantity[] = $row["quantity"];
        $id[] = $row["productId"];
		$color[] = $row["color"];
		$size[] = $row["size"];
    }
    
    $i=0;
    while ( $i < sizeof($id) )
    {
        $sql = "UPDATE `subproducts` 
                SET
                `quantity` = `quantity` - ".$quantity[$i]."
                WHERE 
				`productId` = '".$id[$i]."'
				AND
				`color` = '".$color[$i]."'
				AND
				`size` = '".$size[$i]."'
                ";
        $result = $dbconnect->query($sql);
        $i++;
    }
}
$sql = "SELECT `voucher`
		FROM `orders`
		WHERE 
		`orderId` = '".$orderId."'
		GROUP BY `orderId`
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$voucher = $row["voucher"];

$sql = "SELECT *
		FROM `vouchers`
		WHERE 
		`id` = '".$voucher."'
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$voucher = $row["voucher"];

$sql = "SELECT *
		FROM `orders`
		WHERE 
		`orderId` = '".$orderId."'
		GROUP BY `orderId`
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$email = $row["email"];
?>
<div class="sec-pad grey-bg">
    <div class="container-fluid">
        <div class="row d-flex justify-content-center">
            <div class="col-lg-9">
                <div class="profile-box bordered-box">
                    <div class="profile-sec">
                    <div style="text-align:left">
                    <img src="img/blackLogo.png" style="width:100px;height:100px">
                    </div>
                    <h5 class="page-title"><?php echo $OrderReceivedText ?></h5>
                        <p class="mb-4"><?php echo $OrderReceivedMsgText ?></p>
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-6">
                                <p class="bold"><?php echo $orderNumberText ?></p>
                                <p><?php echo $orderId ?></p>
                            </div>
                            <div class="col-md-3 col-sm-6 col-6">
                                <p class="bold"><?php echo $dateText ?></p>
                                <p><?php echo date("yy/m/d"); ?></p>
                            </div>
                            <div class="col-md-3 col-sm-6 col-6">
                                <p class="bold"><?php echo $deliveryText ?></p>
                                <p><?php echo $row["d_s_charges"] ?>KD</p>
                            </div>
                            <div class="col-md-3 col-sm-6 col-6">
                                <p class="bold"><?php echo $Voucher ?></p>
                                <p><?php echo $voucher ?></p>
                            </div>
                            <div class="col-md-3 col-sm-6 col-6">
                                <p class="bold"><?php echo $discountText ?></p>
                                <p>%<?php echo $row["discount"] ?></p>
                            </div>
                            <div class="col-md-3 col-sm-6 col-6">
                                <p class="bold"><?php echo $totalPriceText ?></p>
                                <p><?php echo $row["totalPrice"] ?>KD</p>
                            </div>
                            <div class="col-md-3 col-sm-6 col-6">
                                <p class="bold"><?php echo $paymentMethodText ?></p>
                                <p><?php if ( $row["pMethod"] == 1 ){ echo "K-NET"; } elseif($row["pMethod"] == 3) { echo "CASH"; } else { echo "Visa/Master"; }; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="profile-sec">
                        <h5 class="page-title"><?php echo $orderDetails ?></h5>
                        <div class="row">
                            <div class="col-sm-4 d-flex justify-content-between">
                                <p class="bold"><?php echo $numberOfProductsText ?></p>
                                <p class="bold">:</p>
                            </div>
                            <div class="col-sm-8">
                                <p><?php echo sizeof($id) ?></p>
                            </div>
                            <div class="col-sm-4 d-flex justify-content-between">
                                <p class="bold"><?php echo $deliveryTimeText ?></p>
                                <p class="bold">:</p>
                            </div>
                            <div class="col-sm-8">
                                <p><?php echo $deliveryPeriodText ?></p>
                            </div>
							<div class="col-sm-4 d-flex justify-content-between">
                                <p class="bold"><?php echo $emailText ?></p>
                                <p class="bold">:</p>
                            </div>
                            <div class="col-sm-8">
                                <p><?php echo $row["email"] ?></p>
                            </div>
							<div class="col-sm-4 d-flex justify-content-between">
                                <p class="bold"><?php echo $deliverToText ?></p>
                                <p class="bold">:</p>
                            </div>
                            <div class="col-sm-8">
                                <p><?php echo $row["fullName"] ?></p>
                            </div>
							<div class="col-sm-4 d-flex justify-content-between">
                                <p class="bold"><?php echo $Mobile ?></p>
                                <p class="bold">:</p>
                            </div>
                            <div class="col-sm-8">
                                <p><?php echo $row["mobile"] ?></p>
                            </div>
                            <div class="col-sm-4 d-flex justify-content-between">
                                <p class="bold"><?php echo $addressText ?></p>
                                <p class="bold">:</p>
                            </div>
                            <div class="col-sm-8">
                                <p>
								<?php
								if ( $row["place"] == "1" )
								{
                                    echo $row["country"] . ", " . $row["area"] . "<br> $blockText " . $row["block"] . ", $streetText " . $row["street"] ;
                                    if ( !empty($row["avenue"]) )
                                    {
                                        echo ", $avenueText " . $row["avenue"];
                                    }

                                    echo ", $houseText " . $row["house"];
								}
								elseif ($row["place"] == "2")
								{
                                    echo $row["country"] . ", " . $row["area"] . "<br> $blockText " . $row["blockA"] . ", $streetText " . $row["streetA"];
                                    
                                    if ( !empty($row["avenueA"]) )
                                    {
                                        echo ", $avenueText " . $row["avenueA"];
                                    }

                                    echo ", $buildingText " . $row["building"] . ", $floorText " . $row["floor"] . ", $apartmentText " . $row["apartment"];
								}
								else
								{
								    echo 'Pick up';
								}
								?>
								</p>
                            </div>
                        </div>
                    </div>
                    <div class="profile-sec">
                        <h5 class="page-title"><?php echo $productsText ?></h5>
                            <div class="checkoutsidebar">
							<?php
							$i = 0;
							while ( $i < sizeof($id) )
							{
								$sql = "SELECT p.*, i.imageurl
										FROM `products` AS p
										JOIN `images` AS i
										ON p.id = i.productId
										WHERE p.id = '".$id[$i]."'
										";
								$result = $dbconnect->query($sql);
								$row = $result->fetch_assoc();
							?>
                                <div class="checkoutsidebar-item">
                                    <span class="quantity">
									<?php 
									if ( isset ($_POST["qorder$i"]) AND $_POST["qorder$i"] != $_SESSION["cart"]["qorder"][$i] )
									{
										echo $_POST["qorder$i"]; 
										$_SESSION["cart"]["qorder"][$i] = $_POST["qorder$i"];
									}
									else
									{
										echo $quantity[$i];
									}
									?>
									</span>
                                    <span class="multiplier">x</span>
                                    <span class="iteminfo"><?php if ( $directionHTML == "rtl" ) {echo $row["arTitle"];} else {echo $row["enTitle"];} ?> [<?php echo $_SESSION["cart"]["color"][$i] ?>] [<?php echo $_SESSION["cart"]["size"][$i] ?>] [<?php echo $_SESSION["cart"]["length"][$i] ?>]</span>
                                    <span class="Price">
									<?php 
										if ( isset($row["discount"]) AND $row["discount"] != "0" )
										{
											echo $price2 = $row["price"] - ( $row["price"] * $row["discount"] / 100 );
										}
										else
										{
											echo $price2 = $row["price"];
										} ?>KD
									</span>
                                </div>
							<?php
								$totals2[] = $price2 * $quantity[$i];
								$i++;
							}
							?>
                            </div>
                            <div class="checkoutsidebar-calculation">
                            </div>
                            <button 
                            type="button" 
                            onclick="window.print()" 
                            class="btn btn-dark">
                            <?php echo $printText ?>
                            </button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
if ( isset($email) )
{
    $to = $email;
    $subject = "Order #$orderId - Create-KW";
    $headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n" . 'From:noreply@create-kw.com';
    $msg = '<html><body><center>
            <img src="http://createkwservers.com/rrcoffeebar/img/logo.jpg" style="width:200px;height:200px">
            <p>&nbsp;</p>
            <p>Dear '.$email.' owner,</p>
            <p>Please keep this ID safe and check your order status with this ID on: <strong> <a href="https://create-kw.com/">Create-KW.com</a></strong>.</p>
            <p>Your order ID is:<br>
            </p>
            <p style="font-size: 50px; color: red"><strong>'.$orderId.'</strong></p>
            <p>Best regards,<br>
            <strong>Create-KW</strong></p>
            </center></body></html>';
    $message = html_entity_decode($msg);
    mail($to, $subject, $msg, $headers);
}
$to = "info@create-kw.com";
    $subject = "Order #$orderId - Create-KW";
    $headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n" . 'From:noreplay@create-kw.com';
    $msg = '<html><body><center>
            <img src="http://createkwservers.com/rrcoffeebar/img/logo.jpg" style="width:200px;height:200px">
            <p>&nbsp;</p>
            <p>Dear info@create-kw.com,</p>
            <p>Your have recived a new order with order ID:<br>
            </p>
            <p style="font-size: 25px; color: red"><strong>'.$orderId.'</strong></p>
            <p>Best regards,<br>
            <strong>info@create-kw.com</strong></p>
            </center></body></html>';
    $message = html_entity_decode($msg);
    mail($to, $subject, $msg, $headers);
session_destroy();
?>