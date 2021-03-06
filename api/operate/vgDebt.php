<?php
include_once("../../funcs.php");
if (isset($_POST['method_id'], $_POST['vg_id'], $_POST['currency_sum'])) {
    include_once("../../db.php");
    include_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";
    $vg_purchase_type = VG_PURCHASE_TYPE;
    $vg_id = clean($_POST['vg_id']);
    $method_id = clean($_POST['method_id']);
    $currency_sum = clean($_POST['currency_sum']);
    if(!isset($_SESSION))
    session_start();
    $user_id = $_SESSION['id'];
    $branch_id = $_SESSION['branch_id'];


    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && heCan($user_data['role'], 1)) {

        $update_vg_debt = $connection->query("
            UPDATE `payments` 
            SET `sum` = `sum` - $currency_sum 
            WHERE `vg_data_debt_id` = '$vg_id' AND fiat_id = '$fiat_id'
            
        ");

        $addOutgo = ($connection->query("
           INSERT INTO `outgo`
           (`user_id`, `method_id`, `outgo_type_id`, `date`, `sum`,  branch_id, vg_data_id) 
           VALUES 
           ('$user_id','$method_id','$vg_purchase_type',now(),'$currency_sum', '$branch_id', '$vg_id' )"));


        if ($update_vg_debt && $addOutgo && updateMethodMoney($connection, $method_id, -$currency_sum)) {
            echo json_encode(array("status" => "success"));
            return false;
        } else {
            return error("failed");
        }
    } else {
        return error("denied");
    }
} else {
    return error("empty");
}