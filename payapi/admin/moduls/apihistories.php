	
    
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
					
					?>

                <!-- Row -->
				<div class="row">
					<div class="col-sm-12">
						<div class="panel panel-default card-view">
							
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
								
									<div class="table-wrap">
										<div class="table-responsive">
											<table id="datable_1" class="table table-hover display  pb-30" >
												<thead>
													<tr>
                                                        <th><?php echo $lang['sn'] ?></th>
                                                        <th>Api Key</th>
                                                        <th>Endpoint</th>
														<th>Status</th>
														<th>Message</th>
														<th>Created at</th>
                                                        <th><?php echo $lang['actions'] ?></th>
													</tr>
												</thead>
												<tfoot>
													<tr>
                                                        <th><?php echo $lang['sn'] ?></th>
                                                        <th>Api Key</th>
                                                        <th>Endpoint</th>
														<th>Status</th>
														<th>Message</th>
														<th>Created at</th>
                                                        <th><?php echo $lang['actions'] ?></th>
													</tr>
												</tfoot>
												<tbody>
                                               <?php 
														$tbl_name = 'tbl_apihistories';
														$where = "";
														$other =" ORDER BY `hid` DESC";
														
														$query = $obj->select_data($tbl_name,$where,$other);
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
																	$created_at = $row['created_at'];
																	?>

																	  <tr style="border-color:#<?=($status=='success'?'28a745':'dc3545')?>">
																		<td><?php echo $sn++; ?>. </td>
																		<td><?php echo $api_key; ?></td>
																		<td><?php echo $endpoint; ?></td>
																		<td style="color:#<?=($status=='success'?'28a745':'dc3545')?>"><?php echo $status; ?></td>
																		<td><?php echo $msg; ?></td>
																		<td><?php echo $created_at; ?></td>
																		<td>
																		<a href="<?php echo SITEURL; ?>admin/index.php?page=apiview&hid=<?php echo $id; ?>" class="btn-success btn-sm">View</a>  
																			<!-- <a href="<?php echo SITEURL; ?>admin/index.php?page=edit_ads&cid=<?php echo $cid; ?>&id=<?php echo $id; ?>" class="btn-success btn-sm"><?php echo $lang['edit'] ?></a>  
																			<a href="<?php echo SITEURL; ?>admin/moduls/delete.php?page=ads&cid=<?php echo $cid; ?>&id=<?php echo $id; ?>" class="btn-error btn-sm"><?php echo $lang['delete'] ?></a> -->
																		</td>
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