<?php
$name = $_POST["name"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$subject = $_POST["subject"];
$message = $_POST["message"];
 
$EmailTo = "info@hbqphoto.com";
//$EmailTo = "HBQ.photo@gmail.com";
//prepare email body text
$Body .= "Subject: ";
$Body .= $subject;
$Body .= "\n";

$Body .= "Name: ";
$Body .= $name;
$Body .= "\n";
 
$Body .= "Email: ";
$Body .= $email;
$Body .= "\n";

$Body .= "Phone: ";
$Body .= $phone;
$Body .= "\n";
 
$Body .= "Message: ";
$Body .= $message;
$Body .= "\n";
 
// send email
$success = mail($EmailTo, $subject, $Body, "From:".$email);
 
// redirect to success page
if ($success){
   echo "success";
}else{
    echo "invalid";
}
 
?>