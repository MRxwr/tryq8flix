<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");
?>
<!DOCTYPE html>
<html>
<title>View Reports - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
.boxsizingBorder {
    -webkit-box-sizing: border-box;
       -moz-box-sizing: border-box;
            box-sizing: border-box;
}
	/* DivTable.com */
.divTable{
	display: table;
	width: 100%;
}
.divTableRow {
	display: table-row;
}
.divTableHeading {
	background-color: #EEE;
	display: table-header-group;
}
.divTableCell, .divTableHead {
	border: 1px solid #999999;
	display: table-cell;
	padding: 3px 10px;
}
.divTableHeading {
	background-color: #EEE;
	display: table-header-group;
	font-weight: bold;
}
.divTableFoot {
	background-color: #EEE;
	display: table-footer-group;
	font-weight: bold;
}
.divTableBody {
	display: table-row-group;
}
</style>

<body>
<!-- Page Container -->
<div class="w3-content" style="max-width:1300px">

  <!-- The Grid -->
  <div class="">
  <?php
include_once ("template/header.php");
?>
    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px">
      <div class="w3-row-padding w3-padding-16 w3-center">
<h2 style="color: white">Users Reports</h2>
    <?php
		if ( isset($username) AND in_array($username,$usernames) ){
			$sql = "SELECT
					p.category, p.title , u.username, r.*
					FROM `reports` as r
					JOIN `posts` as p
					ON
					p.catid = r.catid
					AND
					p.id = r.postid
					JOIN `users` as u
					ON
					r.userid = u.id
					WHERE
					r.status != 'Done'
					ORDER BY r.id DESC
					";
			$result = $dbconnect->query($sql);
			while( $row = $result->fetch_assoc() ){
							?>
			<table width="100%" border="1" style="color: black; 
				background: <?php if ( $row["status"] == "Done" ) { echo "green"; } else { echo "darkgrey"; } ?>">
			  <tbody>
				<tr>
				  <td>Username</td>
				  <td width="90%"><?php echo $row["username"] ?></td>
				</tr>
				<tr>
				  <td>Title</td>
				  <td width="90%"><a target="_blank" href="<?php echo "watch.php?postid=".$row["postid"]."&catid=".$row["catid"] ?>"><?php echo $row["category"] . " " . $row["title"] ?></a></td>
				</tr>
				<tr>
				  <td>Issue</td>
				  <td width="90%"><?php echo $row["issue"] ?></td>
				</tr>
				<tr>
				  <td>Description</td>
				  <td width="90%"><?php echo $row["description"] ?></td>
				</tr>
				<tr>
				  <td>Date</td>
				  <td width="90%"><?php echo $row["date"] ?></td>
				</tr>
				<tr>
				  <td>Status</td>
				  <td width="90%"><?php echo $row["status"] ?></td>
				</tr>
				<tr>
				  <td colspan="2"><a href="includes/donereport.php?reportid=<?php echo $row["id"] ?>">Done</a></td>
				</tr>
			  </tbody>
			</table>
					  <p></p>

						<?php
			}
		}
    ?>
    
  </div>
    </div>
<?php
include_once ("template/footer.php");
?>
    <!-- End Right Column -->
    </div>


</body>
</html>
