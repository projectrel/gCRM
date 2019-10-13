<?php
include_once("../../funcs.php");
if (!isset($_GET['rollback_paying_id']))
    return error("failed");
include_once("../../db.php");
$rollback_paying_id = clean($_GET['rollback_paying_id']);
$rollback_data = mysqli_fetch_assoc($connection->query("
    SELECT * FROM `rollback_paying` WHERE `rollback_paying_id` = '$rollback_paying_id'
    "));

if ($rollback_data) {
    echo json_encode($rollback_data);
    return false;
} else {
    return error("failed");
}



