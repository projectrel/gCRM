<?php
include_once("../../funcs.php");
if (!isset($_POST['outgo_sum'], $_POST['method_id'], $_POST['outgo_id']))
    return error("empty");
include_once("../../db.php");
$outgo_sum = !empty($_POST['outgo_sum']) ? $_POST['outgo_sum'] : "NULL";
$owner_id = !empty($_POST['client_id']) && $_POST['client_id'] != "branch" ? $_POST['client_id'] : "NULL";
$method_id = !empty($_POST['method_id']) ? $_POST['method_id'] : "NULL";
$project_id = !empty($_POST['project_id']) ? $_POST['project_id'] : "NULL";
$outgo_type = !empty($_POST['outgo_type']) ? $_POST['outgo_type'] : "NULL";
$outgo_id = $_POST['outgo_id'];
$description = !empty($_POST['description']) ? $_POST['description'] : "NULL";

if(!isset($_SESSION))
    session_start();
$user_id = $_SESSION['user_id'];
$user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
if (!heCan($user_data['role'], 2))
    return error("denied");
$branch = $_SESSION['branch_id'];
$branch_id = $owner_id != "branch" ? "NULL" : $branch;



$old_outgo_data = mysqli_fetch_assoc($connection->
query("SELECT * FROM `outgo` WHERE  `outgo_id`='$outgo_id' "));

$update_old_method = updateMethodMoney($connection, $old_outgo_data['method_id'], $old_outgo_data['sum'] );
$update_method = updateMethodMoney($connection, $method_id, $outgo_sum);
if(!($update_old_method && $update_method))
    return error("custom", "Не удалось обновить данные счетов");

$update_query = "UPDATE `outgo` SET 
`user_as_owner_id`=$owner_id,`branch_id`=$branch_id,`method_id`=$method_id,
`outgo_type_id`= $outgo_type,`sum`=$outgo_sum,`description`=$description,
`project_id`=$project_id WHERE `outgo_id` = '$outgo_id'";
$res = $connection->query($update_query);
if (!($res && updatemethodMoney($connection, $method_id, -$outgo_sum) && save_change_info($connection,'outgo',$outgo_id)))
    return error("failed");



echo json_encode(array("status" => "success"));
