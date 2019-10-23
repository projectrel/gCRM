<?php
include_once("../../funcs.php");
if (!isset($_POST['name']))
    return error("empty");

include_once("../../db.php");

$name = clean($_POST['name']);
$vg_id = clean($_POST['vg_id']);
if(!isset($_SESSION))
    session_start();
$user_id = $_SESSION['user_id'];
$user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
if (!heCan($user_data['role'], 3))
    return error("denied");

$res = $connection->
query("
        UPDATE `virtualgood`
        SET
            `name` = '$name'
        WHERE 
            `vg_id`='$vg_id' 
        ");
if (!($res && save_change_info($connection, 'vg', $vg_id)))
    return error("failed");

echo json_encode(array("status" => "edit-success"));



