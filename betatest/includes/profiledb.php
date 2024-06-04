<?php

session_start();

include_once ("config.php");
$username = $_POST["username"];
$result = $dbconnect->query("SELECT * FROM users WHERE username='$username'");
$row = $result->fetch_assoc();

$username = $_POST["username"];
$password = $_POST["password"];
$email = $_POST["email"];
$description = $_POST["description"];

if ( !preg_match("/^[a-zA-Z0-9 ]*$/",$password) )
	{
		header("Location: ../register.php?error=2");
		exit();
	}
if ( !preg_match("/^[a-zA-Z0-9 ]*$/",$description) )
	{
		header("Location: ../editprofile.php?error=3");
		exit();
	}


if ( isset($_FILES["fileToUpload"]) )
{
$target_dir = "../avatar/";
$originalfilename = explode (".",basename( $_FILES["fileToUpload"]["name"]));
$uploadOk = 1;
$target_file = $target_dir . round(microtime(true)). "." .$originalfilename[1];
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists."."<br><br>";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large."."<br><br>";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded."."<br><br>";
// if everything is ok, try to upload file
} else {
	$target_file1 = $target_dir . round(microtime(true)). "." .$originalfilename[1];
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file1)) {
		$title = basename( $_FILES["fileToUpload"]["name"]);
		$title = explode(".jpg",$title);
    } else {
        echo "Sorry, there was an error uploading your file."."<br><br>";
    }
}
}

if ( $password != $row["password"] )
{
	$password = md5($password);
	$sql = "UPDATE users SET password='$password' WHERE username='$username'";
	$results = $dbconnect->query($sql);
}

if ( $email != $row["email"] )
{
	$sql = "UPDATE users SET email='$email' WHERE username='$username'";
	$results = $dbconnect->query($sql);
}

if ( isset ($target_file1) )
{
	$target_file1 = explode("..",$target_file);
	$avatar = $target_file1[1];
	$sql = "UPDATE users SET avatar='$avatar' WHERE username='$username'";
	$results = $dbconnect->query($sql);
}

if ( $description != $row["description"] )
{
	$sql = "UPDATE users SET description='$description' WHERE username='$username'";
	$results = $dbconnect->query($sql);
}

header ("Location: ../profile.php?username=$username");

?>