<?php
include_once("../../funcs.php");
if (!isset($_POST['method_from'], $_POST['method_to'], $_POST['sum_from'], $_POST['sum_to']))
    return error("empty");
include_once("../../db.php");
$method_from = clean($_POST['method_from']);
$method_to = clean($_POST['method_to']);
$sum_from = clean($_POST['sum_from']);
$sum_to = clean($_POST['sum_to']);
if (!isset($_SESSION))
    session_start();
$user_id = $_SESSION['id'];
$user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
if (!($user_data && (heCan($user_data['role'], 1))))
    return error("denied");

$update_method_from = $connection->query("UPDATE payments SET `sum` = `sum` - '$sum_from' WHERE `method_id` = '$method_from'");
$update_method_to = $connection->query("UPDATE payments SET `sum` = `sum` + '$sum_to' WHERE  `method_id` = '$method_to'");

if (!($update_method_from && $update_method_to))
    return error("failed");

echo json_encode(array("status" => "success", "success" => true));
