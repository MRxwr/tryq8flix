<!DOCTYPE html>
<html>
<body>

<form action="" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="subtitle" id="fileToUpload">
    <input type="submit" value="Upload Subtitle" name="submit">
</form>
<br>
</body>
</html>

<?php
if ( isset($_FILES["subtitle"]) )
{
$target_dir = "subs/";
$target_file = $target_dir . basename($_FILES["subtitle"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    if (move_uploaded_file($_FILES["subtitle"]["tmp_name"], $target_file)) {
		$title = basename( $_FILES["subtitle"]["name"]);
		$title = explode(".srt",$title);
        echo $title[0];
    } else {
        echo "Sorry, there was an error uploading your file."."<br><br>";
    }
	echo "<br>". mb_detect_encoding($target_file);
}
?>