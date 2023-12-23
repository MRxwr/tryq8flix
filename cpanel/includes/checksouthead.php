<?php
if ( isset ( $_COOKIE["CreateKWUOudnaA"] ) ){
	session_start ();
	require ("config.php");
	
	$svdva = $_COOKIE["CreateKWUOudnaA"];
	
	$sql = "SELECT * 
			FROM `adminstration` 
			WHERE `keepMeAlive` LIKE '%".$svdva."%'";
	$result = $dbconnect->query($sql);
	if ( $result->num_rows == 1 ){
		$row = $result->fetch_assoc();
		$userID = $row["id"];
		$email = $row["email"];
		$username = $row["fullName"];
		$_SESSION['CreateKWUOudnaA'] = $email;	
	}
	else{
		$sql = "SELECT * 
				FROM `employees` 
				WHERE `keepMeAlive` LIKE '%".$svdva."%'";
		$result = $dbconnect->query($sql);
		if ( $result->num_rows == 1 ){
			$row = $result->fetch_assoc();
			$userID = $row["id"];
			$email = $row["email"];
			$username = $row["fullName"];
			$_SESSION['CreateKWUOudnaA'] = $email;	
		}else{
			header("Location: logout.php");
		}
	}
}
elseif ( !isset ( $_COOKIE["CreateKWUOudnaA"] ) ){
	header("Location: login.php");
}
?>