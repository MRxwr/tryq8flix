
                                               <?php 
                                               include('../../languages/lang_config.php');
                                               include('../config/apply.php');
												## Read value
												$draw = $_POST['draw'];
												$row = $_POST['start'];
												$rowperpage = $_POST['length']; // Rows display per page
												$columnIndex = $_POST['order'][0]['column']; // Column index
												$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
												$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
												$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value

												## Search 
												$searchQuery = " ";
												if($searchValue != ''){
												$searchQuery = " and (api_key like '%".$searchValue."%' or 
														endpoint like '%".$searchValue."%' or 
														status like'%".$searchValue."%' ) ";
												}

												## Total number of records without filtering
												$sel = mysqli_query($conn,"select count(*) as allcount from tbl_apihistories");
												$records = mysqli_fetch_assoc($sel);
												$totalRecords = $records['allcount'];

												## Total number of record with filtering
												$sel = mysqli_query($conn,"select count(*) as allcount from tbl_apihistories WHERE 1 ".$searchQuery);
												$records = mysqli_fetch_assoc($sel);
												$totalRecordwithFilter = $records['allcount'];

												## Fetch records
												$empQuery = "select * from tbl_apihistories WHERE 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
												$empRecords = mysqli_query($conn, $empQuery);
												
												$data = array();

												while ($row = mysqli_fetch_assoc($empRecords)) {
												     // Delete Button
                                                 $viewButton = "<a href=".SITEURL."admin/index.php?page=apiview&hid=".$row['hid']." class='btn-success btn-sm'>View</a>";
                                            
                                                $action = $viewButton;
												$data[] = array( 
												    "hid"=>$row['hid'],
													"api_key"=>$row['api_key'],
													"endpoint"=>$row['endpoint'],
													"status"=>$row['status'],
													"msg"=>$row['msg'],
													"created_at"=>$row['created_at'],
													"action" => $action
												);
												}

												## Response
												$response = array(
												"draw" => intval($draw),
												"iTotalRecords" => $totalRecords,
												"iTotalDisplayRecords" => $totalRecordwithFilter,
												"aaData" => $data
												);

												echo json_encode($response);

											?>
												