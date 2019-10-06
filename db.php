<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

//$connection = mysqli_connect("localhost", "gcrm1", "9834cm9834ME", "dev_gcrm");


mysqli_query($connection, "SET NAMES 'utf8'");
mysqli_query($connection, "SET CHARACTER SET 'utf8'");


