	
    
    	<!-- Title -->
				<div class="row heading-bg">
					<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
						<h5 class="txt-dark">Api Call Histories</h5>
					</div>
					<?php 
						if(isset($_SESSION['add']))
						{
							echo $_SESSION['add'];
							unset($_SESSION['add']);
						}
						if(isset($_SESSION['edit']))
						{
							echo $_SESSION['edit'];
							unset($_SESSION['edit']);
						}
						if(isset($_SESSION['delete']))
						{
							echo $_SESSION['delete'];
							unset($_SESSION['delete']);
						}
						if(isset($_GET['hid']))
						{
							$hid =  $_GET['hid'];
						
						}else{
							$hid = 0;
						}
					
					?>

                <!-- Row -->
				<div class="row">
					<div class="col-sm-12">
						<div class="panel panel-default card-view">
							
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
								
									<div class="table-wrap">
										<div class="table-responsive">
											<table id="datable_1_oldx" class="table table-hover display  pb-30" >
												<thead>
													<tr>
                                                        <th><?php echo $lang['sn'] ?></th>
                                                        <th>Api Key</th>
                                                        <th>Endpoint</th>
														<th>Status</th>
														<th>Message</th>
													</tr>
												</thead>
												
												<tbody>
                                               <?php 
														$tbl_name = 'tbl_apihistories';
														$where = "hid=$hid";
														
														$query = $obj->select_data($tbl_name,$where);
														$res = $obj->execute_query($conn,$query);
														$sn = 1;

														if($res)
														{
															$count_rows= $obj->num_rows($res);
															if($count_rows > 0)
															{
																while ($row=$obj->fetch_data($res)) {
																	$id = $row['hid'];
																	$api_key= $row['api_key'];
																	$endpoint = $row['endpoint'];
																	$status = $row['status'];
																	$msg = $row['msg'];
																	$response = json_decode($row['response'], true);
																	?>

																	  <tr>
																		<td><?php echo $sn++; ?>. </td>
																		<td><?php echo $api_key; ?></td>
																		<td><?php echo $endpoint; ?></td>
																		<td><?php echo $status; ?></td>
																		<td><?php echo $msg; ?></td>
																	</tr>
																	<tr>
																		<td colspan="5"><?php print_r($response);?> </td>	
																	</tr>

																	<?php
																}
															}
															else
															{
																echo "<tr><td colspan='5' class='error'>No Advertise slider Found.</td></tr>";
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
	<?php 