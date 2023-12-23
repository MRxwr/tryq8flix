	
    
    	<!-- Title -->
				<div class="row heading-bg">
					<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
						<h5 class="txt-dark"><?php echo $lang['appkey'] ?></h5>
					</div>a
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
						$id =1;
							$tbl_name = 'tbl_settings';
							$where = "id='$id'";

							$query = $obj->select_data($tbl_name,$where);
							$res = $obj->execute_query($conn,$query);

							if($res==true)
							{
								$count_rows = $obj->num_rows($res);
								if($count_rows==1)
								{
									$row = $obj->fetch_data($res);
									$ads_title_en  = $row['ads_title_en'];
									$ads_title_ar  = $row['ads_title_ar'];
									
								}
							}
					?>

                <!-- Row -->
				<div class="row">
					<div class="col-sm-12">
						<div class="panel panel-default card-view">
							<div class="panel-heading">
								<div class="pull-left">
								<?php if(in_array('add_appkey',$user_permission)){?>
                                        <a href="<?php echo SITEURL; ?>admin/index.php?page=add_appkey">
                                            <button class="btn-primary btn-sm"><?php echo $lang['add'] ?></button>
                                        </a>
								<?php } ?>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
								
									<div class="table-wrap">
										<div class="table-responsive">
											<table id="datable_1" class="table table-hover display  pb-30" >
												<thead>
													<tr>
                                                        <th><?php echo $lang['sn'] ?></th>
                                                        <th><?php echo $lang['title'] ?></th>
														<th>Api Provider</th>
                                                        <th><?php echo ($lang['api_key']?$lang['api_key']:'Api Key') ?></th>
                                                        <th>Api Mode</th>
                                                        <th><?php echo $lang['is_active'] ?></th>
                                                        <th><?php echo $lang['actions'] ?></th>
													</tr>
												</thead>
												<tfoot>
													<tr>
                                                        <th><?php echo $lang['sn'] ?></th>
                                                        <th><?php echo $lang['title'] ?></th>
														<th>Api Provider</th>
                                                        <th><?php echo ($lang['api_key']?$lang['api_key']:'Api Key') ?></th>
                                                        <th>Api Mode</th>
                                                        <th><?php echo $lang['is_active'] ?></th>
                                                        <th><?php echo $lang['actions'] ?></th>
													</tr>
												</tfoot>
												<tbody>
                                               <?php 
                                                        $tbl_name = "tbl_apikeys";
														$where = "id > 0 order by id desc";
														$query = $obj->select_data($tbl_name,$where);
														$res = $obj->execute_query($conn,$query);
														$sn = 1;

														if($res)
														{
															$count_rows= $obj->num_rows($res);
															if($count_rows > 0)
															{
																while ($row=$obj->fetch_data($res)) {
																	$id = $row['id'];
																	$api_key = $row['api_key'];
																	$title = $row['title_'.$_SESSION['lang']];
																	$provider = $row['provider'];
																	$mode = $row['mode'];
																	$is_active = $row['is_active'];
																	
																	?>

																	  <tr>
																		<td><?php echo $sn++; ?>. </td>
																		<td><?php echo $title; ?></td>
																		<td><?php echo $provider; ?></td>
																		<td><?php echo $api_key; ?></td>
																		<td><?php echo $mode; ?></td>
																		<td>
																			<?php if($is_active=='Yes'){echo $lang['yes'];}else if($is_active=='No'){echo $lang['no'];} ?>
																		</td>
																		<td>
																			<a href="<?php echo SITEURL; ?>admin/index.php?page=edit_appkey&id=<?php echo $id; ?>" class="btn-success btn-sm"><?php echo $lang['edit'] ?></a>  
																			<!--<a href="<?php echo SITEURL; ?>admin/moduls/delete.php?page=apikeys&cid=<?php echo $cid; ?>&id=<?php echo $id; ?>" class="btn-error btn-sm"><?php echo $lang['delete'] ?></a>-->
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
					<?php 
								if(isset($_POST['submit']))
								{
									$id =1;
									$ads_title_en  = $obj->sanitize($conn,$_POST['ads_title_en']);
									$ads_title_ar = $obj->sanitize($conn,$_POST['ads_title_ar']);
									$data="
									ads_title_en='$ads_title_en',
									ads_title_ar='$ads_title_ar'
									";
									$where = "id='$id'";
									$tbl_name = 'tbl_settings';
									$query = $obj->update_data($tbl_name,$data,$where);
									$res = $obj->execute_query($conn,$query);
									if($res==true)
									{
										$_SESSION['edit'] = "<div class='success'>".$lang['edit_success']."</div>";
										header('location:'.SITEURL.'admin/index.php?page=ads');
									}
									else
									{
										$_SESSION['edit'] = "<div class='error'>".$lang['edit_fail']."</div>";
										header('location:'.SITEURL.'admin/index.php?page=ads&id='.$id);
									}
								}
							?>
				</div>
				<!-- /Row -->
    </div>
	<?php 