<?php
include_once ("config.php");

if ( isset ( $_POST["email"] ))
{
$email = $_POST["email"];
$randomcode = md5(rand());

$sql = "
UPDATE adminstration
SET forgetpassword = '$randomcode'
WHERE email = '$email';
";
$result = $dbconnect->query($sql);

$sql = "SELECT fullName FROM adminstration WHERE email = '$email'";
$result = $dbconnect->query($sql);
if ( $result->num_rows > 0 )
{
	$row = $result->fetch_assoc();
	$username = $row["fullName"];
	$to = $email;
	$subject = "Forget Password - Blizzard-KW";
	$headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n" . 'From: noreply@blizzard-kw.com';
	$msg = '<html><body><center>
			<p><a href="http://blizzard-kw.com/"><img src="https://i.imgur.com/Kawd6uL.png" style="width:250px;height:250px"></a></p>
			<p>&nbsp;</p>
			<p>Dear '.$UserfullName.',</p>
			<p>You are receiving this e-mail because you requested a password reset for your user account at<strong> Blizzard-kw.com</strong>.</p>
			<p>Please copy to the following code to renew your password:<br>
			</p>
			<p style="font-size: 50px; color: red"><strong>'.$randomcode.'</strong></p>
			<p>Best regards,<br>
			<strong>Blizzard-KW Team</strong></p>
			</center></body></html>';
	$message = html_entity_decode($msg);
	mail($to, $subject, $msg, $headers);
	header("Location: ../login.php?fgp=check");
}
else
{
	header("Location: ../login.php?error=dklhjasdkl");
}
}
elseif ( isset ($_POST["check"]) )
{
	$randomcode = $_POST["check"];
	$sql = "SELECT * FROM adminstration WHERE forgetpassword = '$randomcode'";
	$result = $dbconnect->query($sql);
	
	if ( $result->num_rows > 0 )
	{
		$code = md5(rand());
		$sql = "
		UPDATE adminstration
		SET forgetpassword = '$code'
		WHERE forgetpassword = '$randomcode';
		";
		$result = $dbconnect->query($sql);
		header("Location: ../login.php?fgp=rpwd&ccdi=".$code);
	}
	else
	{
		header("Location: ../login.php?error=eqwweqw");
	}
}
elseif ( isset ($_POST["newpwd"]) )
{
	$newpass = md5 ($_POST["newpwd"]);
	$code = $_POST["ccdi"];
	
	$sql = "
	UPDATE adminstration
	SET password = '$newpass'
	WHERE forgetpassword = '$code';
	";
	$result = $dbconnect->query($sql);
	
	$sql = "
	UPDATE adminstration
	SET forgetpassword = ''
	WHERE password = '$newpass';
	";
	$result = $dbconnect->query($sql);
	
	header("Location: ../login.php?msg=npwd");
	
}




?>