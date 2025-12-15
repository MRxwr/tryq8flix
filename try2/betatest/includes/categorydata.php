<?php
$sql = "SELECT * FROM category WHERE id like '$id'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$categorytitle = $row["title"];
$categoryposter = $row["poster"];
$categorytype = $row["type"];
?>