<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/funcs.php";
include_once("../../db.php");
if (!isset($_POST['vg_id'], $_POST['vg_sum'], $_POST['fiat_id'])) {
    return error("empty");
}
if(!isset($_SESSION))
    session_start();
$branch_id = $_SESSION['branch_id'];
$user_id = $_SESSION['id'];
$sum_vg = clean($_POST['vg_sum']);
$vg_id = clean($_POST['vg_id']);
$fiat_id = clean($_POST['fiat_id']);
$sum_currency = mysqli_fetch_assoc($connection->query("SELECT in_percent FROM vg_data WHERE vg_data_id = '$vg_id'"))['in_percent'] / 100 * $sum_vg;
$purchase_unique_key = md5(uniqid(rand(), true));
if (!addPurchase($connection, $user_id, $vg_id, $sum_vg, $sum_currency, $fiat_id, $purchase_unique_key)) {
    return error("failed");
}

if (!updateVGBalance($connection, $vg_id, $sum_vg)) {
    return error("failed");
}
//    if (!updateBranchBalance($connection, $branch_id, $fiat_id, $sum_currency)) {
//        return error("failed");
//    }
//    if (!addOutgo($connection, $user_id, $fiat_id, $sum_currency, $branch_id, $vg_id)) {
//        return error("failed");
//    }
    if (!updateDebtBalance($connection, $fiat_id, $sum_currency, $purchase_unique_key)) {
        return error("failed");
    }


echo json_encode(array("status" => "success"));


function addPurchase($connection, $user_id, $vg_id, $sum_vg, $sum_currency, $fiat_id, $purchase_unique_key)
{
    $vg_purchase_credit = $sum_currency;
    $add_vg_purchase = ($connection->query("
           INSERT INTO `vg_purchases`
           (`user_id`, `vg_data_id`, `fiat_id`, `vg_purchase_sum`,`vg_purchase_sum_currency`, `vg_purchase_credit`, `vg_purchase_on_credit`,`vg_purchase_unique_key`) 
           VALUES 
           ('$user_id','$vg_id','$fiat_id','$sum_vg','$sum_currency','$vg_purchase_credit',1,'$purchase_unique_key')"));
    return $add_vg_purchase;
}

function updateVGBalance($connection, $vg_id, $sum_vg)
{
    $update_balance = ($connection->query("
        UPDATE vg_data SET `vg_amount` = `vg_amount` + '$sum_vg' WHERE `vg_data_id` = '$vg_id'"));
    return $update_balance;
}



function updateDebtBalance($connection, $fiat_id, $sum_currency, $purchase_unique_key)
{
    $vg_data_id = mysqli_fetch_assoc($connection->query("
        SELECT vg_data_id FROM vg_purchases WHERE vg_purchase_unique_key = '$purchase_unique_key'"))['vg_data_id'];

    $payment_is_exists = mysqli_fetch_assoc($connection->query("
         SELECT * FROM payments WHERE fiat_id = '$fiat_id' AND vg_data_debt_id = '$vg_data_id'"));
    if (count($payment_is_exists)) {
        $payment_id = $payment_is_exists['payment_id'];
        $update_debt_balance = $connection->query("
        UPDATE payments SET `sum` = `sum` + '$sum_currency' WHERE payment_id = '$payment_id'");
    } else {
        $update_debt_balance = $connection->query("
        INSERT INTO payments (`fiat_id`, `sum`, `vg_data_debt_id`) VALUES ('$fiat_id', '$sum_currency', '$vg_data_id')");
    }
    return $update_debt_balance;
}















//function addOutgo($connection, $user_id, $fiat_id, $sum, $branch_id, $vg_id)
//{
//    include_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";
//    $vg_purchase_type = VG_PURCHASE_TYPE;
//
//    $addOutgo = ($connection->query("
//           INSERT INTO `outgo`
//           (`user_id`, `fiat_id`, `outgo_type_id`, `date`, `sum`, `branch_id`, `vg_data_id`)
//           VALUES
//           ('$user_id','$fiat_id','$vg_purchase_type',now(),'$sum','$branch_id', '$vg_id' )"));
//    return $addOutgo;
//
//
//}
//function updateBranchBalance($connection, $branch_id, $fiat_id, $sum_currency)
//{
//    $update_balance = ($connection->query("
//        UPDATE payments SET `sum` = `sum` - '$sum_currency' WHERE `branch_id` = '$branch_id' AND `fiat_id` = '$fiat_id'"));
//    return $update_balance;
//}