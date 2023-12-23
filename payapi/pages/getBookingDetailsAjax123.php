<?php 
	include('../languages/lang_config.php');
	include('../admin/config/apply.php');
    include('../includes/functions.php');
if(isset($_POST['searchquery']) &&( $_POST['searchquery'] != "")){
  $searchquery = $_POST['searchquery'];
  $searches=get_bookingSearch($searchquery);
 // print_r($searches);
}		
?>

<div class="panel panel-default card-view">
							<div class="panel-heading">
								<div class="pull-left">
									<h2 class="shoots-Head2">Search Result</h2>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper">
								<div class="panel-body">
									<div class="table-wrap">
										<div class="table-responsive">
											<table id="datable_1" class="table table-hover display  pb-30" >
												<thead>
													<tr>
                                                        <th><?php echo $lang['sn'] ?></th>
                                                        <th><?php echo $lang['package_name'] ?></th>
                                                        <th><?php echo $lang['customer_name'] ?></th>
                                                        <th><?php echo $lang['mobile_number'] ?></th>
                                                        <th><?php echo $lang['baby_name'] ?></th>
                                                        <th><?php echo $lang['baby_age'] ?></th>
                                                        <th><?php echo $lang['instructions'] ?></th>
                                                        <th><?php echo $lang['booking_date'] ?></th>
                                                        <th><?php echo $lang['booking_time'] ?></th>
                                                        <th><?php echo $lang['booking_price'] ?></th>
                                                        <th><?php echo $lang['transaction_id'] ?></th>
													</tr>
												</thead>
												<tfoot>
													<tr>
                                                        <th><?php echo $lang['sn'] ?></th>
                                                        <th><?php echo $lang['package_name'] ?></th>
                                                        <th><?php echo $lang['customer_name'] ?></th>
                                                        <th><?php echo $lang['mobile_number'] ?></th>
                                                        <th><?php echo $lang['baby_name'] ?></th>
                                                        <th><?php echo $lang['baby_age'] ?></th>
                                                        <th><?php echo $lang['instructions'] ?></th>
                                                        <th><?php echo $lang['booking_date'] ?></th>
                                                        <th><?php echo $lang['booking_time'] ?></th>
                                                        <th><?php echo $lang['booking_price'] ?></th>
                                                        <th><?php echo $lang['transaction_id'] ?></th>
													</tr>
												</tfoot>
												<tbody>
													<?php 
													if(@count($searches) != 0){
													$sn = 1;
													foreach($searches as $key=>$searche){ 
													        $package_id = $searche['package_id'];
															$transaction_id = $searche['transaction_id'];
															$customer_name = $searche['customer_name'];
															$mobile_number = $searche['mobile_number'];
															$baby_name = $searche['baby_name'];
															$baby_age = $searche['baby_age'];
															$instructions = $searche['instructions'];
															$booking_date = $searche['booking_date'];
															$booking_time = $searche['booking_time'];
															$booking_price = $searche['booking_price'];
															$is_active = $searche['status'];
															$tbl_name1 = 'tbl_packages';
															$where = 'id='.$package_id;
												            $query1 = $obj->select_data($tbl_name1,$where);
															$res1 = $obj->execute_query($conn,$query1);
															$row = $obj->fetch_data($res1);
															$package_name = $row['title_'.$_SESSION['lang']];
													?>
													<tr>
																<td><?php echo $sn++; ?>. </td>
																<td><?php echo $package_name; ?></td>
																<td><?php echo $customer_name; ?></td>
                                                                <td><?php echo $mobile_number; ?></td>
                                                                <td><?php echo $baby_name; ?></td>
                                                                <td><?php echo $baby_age; ?></td>
                                                                <td><?php echo $instructions; ?></td>
																<td><?php echo $booking_date; ?></td>
																<td><?php echo $booking_time; ?></td>
                                                                <td>$<?php echo $booking_price; ?></td>
                                                                <td><?php echo $transaction_id; ?></td>
															</tr>
                                                    <?php }
													} else { 
														echo "<tr><td colspan='11' class='error'>No Search Data Found.</td></tr>";
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>