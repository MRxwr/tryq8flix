<?php
if ( isset ( $_COOKIE["TRYQ8FliXAdmin"] ) )
{
	session_start ();
	require ("config.php");
	
	$svdva = $_COOKIE["TRYQ8FliXAdmin"];
	
	$sql = "SELECT
			*
			FROM
			`adminstration`
			WHERE
			`keepMeAlive`
			LIKE '%".$svdva."%'
			";
	$result = $dbconnect->query($sql);
	
	if ( $result->num_rows == 1 )
	{
		$row = $result->fetch_assoc();
		$userID = $row["id"];
		$username = $row["username"];
		$email = $row["email"];
		$_SESSION['TRYQ8FliXAdmin'] = $email;	
	}
}
elseif ( !isset ( $_COOKIE["TRYQ8FliXAdmin"] ) )
{
	header("Location: login.php");
}