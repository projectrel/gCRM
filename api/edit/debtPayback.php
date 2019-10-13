<?php
include_once("../../funcs.php");
if (!isset($_POST['debt_id'], $_POST['debt_sum'], $_POST['client_id'], $_POST['method_id']))
    return error("empty");

include_once("../../db.php");

$debt_id = clean($_POST['debt_id']);
$debt_sum = clean($_POST['debt_sum']);
$client_id = clean($_POST['client_id']);
$method_id = clean($_POST['method_id']);

session_start();
$user_id = $_SESSION['user_id'];
$user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
if (!heCan($user_data['role'], 2))
    return error("denied");

$old_debt_data = mysqli_fetch_assoc($connection->
query("SELECT * FROM `debt_history` WHERE  `debt_history_id`='$debt_id' "));
$update_old_method = updateMethodMoney($connection, $old_debt_data['method_id'], -$old_debt_data['debt_sum'] );
$update_method = updateMethodMoney($connection, $method_id, $debt_sum);
if(!($update_old_method && $update_method))
    return error("custom", "Не удалось обновить данные счетов");
$update = $connection->
query("
        UPDATE `debt_history`
        SET
            `debt_sum` = '$debt_sum',
            `client_id` = '$client_id',
            `method_id` = '$method_id'
        WHERE 
            `debt_history_id`='$debt_id' 
        ");
if (!($update && save_change_info($connection, 'debt_history', $debt_id) ))
    return error("failed");

echo json_encode(array("status" => "edit-success"));



