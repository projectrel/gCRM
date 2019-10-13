<?php
include_once("../../funcs.php");
if (!isset($_POST['rollback_paying_id'], $_POST['rollback_sum'], $_POST['client_id'], $_POST['method_id']))
    return error("empty");

include_once("../../db.php");

$rollback_paying_id = clean($_POST['rollback_paying_id']);
$rollback_sum = clean($_POST['rollback_sum']);
$client_id = clean($_POST['client_id']);
$method_id = clean($_POST['method_id']);

session_start();
$user_id = $_SESSION['user_id'];
$user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
if (!heCan($user_data['role'], 2))
    return error("denied");

$old_rollback_payment_data = mysqli_fetch_assoc($connection->
query("SELECT * FROM `rollback_paying` WHERE  `rollback_paying_id`='$rollback_paying_id' "));

$update_old_method = updateMethodMoney($connection, $old_rollback_payment_data['method_id'], $old_rollback_payment_data['rollback_sum'] );
$update_method = updateMethodMoney($connection, $method_id, -$rollback_sum);
if(!($update_old_method && $update_method))
    return error("custom", "Не удалось обновить данные счетов");
$update = $connection->
query("
        UPDATE `rollback_paying`
        SET
            `rollback_sum` = '$rollback_sum',
            `client_id` = '$client_id',
            `method_id` = '$method_id'
        WHERE 
            `rollback_paying_id`='$rollback_paying_id' 
        ");
if (!($update && save_change_info($connection, 'rollback_paying', $rollback_paying_id) ))
    return error("failed");

echo json_encode(array("status" => "edit-success"));



