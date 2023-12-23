	
    
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
											<table id="datable_ex" class="table table-hover display  pb-30" >
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