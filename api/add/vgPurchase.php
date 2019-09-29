<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/funcs.php";
include_once("../../db.php");
if (!isset($_POST['vg_id'], $_POST['vg_sum'], $_POST['on_credit'], $_POST['fiat_id'])) {
    return error("empty");
}
session_start();
$branch_id = $_SESSION['branch_id'];
$user_id = $_SESSION['id'];
$sum_vg = clean($_POST['vg_sum']);
$vg_id = clean($_POST['vg_id']);
$fiat_id = clean($_POST['fiat_id']);
$on_credit = clean($_POST['on_credit']) === "true" ? 1 : 0;
$sum_currency = mysqli_fetch_assoc($connection->query("SELECT in_percent FROM vg_data WHERE vg_data_id = '$vg_id'"))['in_percent'] / 100 * $sum_vg;
$purchase_unique_key = md5(uniqid(rand(), true));
if (!addPurchase($connection, $user_id, $vg_id, $sum_vg, $sum_currency, $fiat_id, $on_credit, $purchase_unique_key)) {
    return error("failed");
}

if (!updateVGBalance($connection, $vg_id, $sum_vg)) {
    return error("failed");
}

if (!$on_credit) {

    if (!updateBranchBalance($connection, $branch_id, $fiat_id, $sum_currency)) {
        return error("failed");
    }
    if (!addOutgo($connection, $user_id, $fiat_id, $sum_currency, $purchase_unique_key)) {
        return error("failed");
    }
}

echo json_encode(array("status" => "success"));


function addPurchase($connection, $user_id, $vg_id, $sum_vg, $sum_currency, $fiat_id, $on_credit, $purchase_unique_key)
{
    $vg_purchase_credit = $on_credit ? $sum_vg : 0;
    $add_vg_purchase = ($connection->query("
           INSERT INTO `vg_purchases`
           (`user_id`, `vg_data_id`, `fiat_id`, `vg_purchase_sum`,`vg_purchase_sum_currency`, `vg_purchase_credit`, `vg_purchase_on_credit`,`vg_purchase_unique_key`) 
           VALUES 
           ('$user_id','$vg_id','$fiat_id','$sum_vg','$sum_currency','$vg_purchase_credit','$on_credit','$purchase_unique_key')"));
    return $add_vg_purchase;
}

function updateVGBalance($connection, $vg_id, $sum_vg)
{
    $update_balance = ($connection->query("
        UPDATE vg_data SET `vg_amount` = `vg_amount` + '$sum_vg' WHERE `vg_data_id` = '$vg_id'"));
    return $update_balance;
}

function updateBranchBalance($connection, $branch_id, $fiat_id, $sum_currency)
{
    $update_balance = ($connection->query("
        UPDATE payments SET `sum` = `sum` - '$sum_currency' WHERE `branch_id` = '$branch_id' AND `fiat_id` = '$fiat_id'"));
    return $update_balance;
}

function addOutgo($connection, $user_id, $fiat_id, $sum, $purchase_unique_key)
{
    include_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";
    $vg_purchase_type = VG_PURCHASE_TYPE;
    $vg_purchase_id = mysqli_fetch_assoc($connection->query("SELECT vg_purchase_id FROM vg_purchases WHERE vg_purchase_unique_key = '$purchase_unique_key'"))['vg_purchase_id'];
    $addOutgo = ($connection->query("
           INSERT INTO `outgo`
           (`user_id`, `fiat_id`, `outgo_type_id`, `date`, `sum`, `vg_purchase_id`) 
           VALUES 
           ('$user_id','$fiat_id','$vg_purchase_type',now(),'$sum', '$vg_purchase_id' )"));
    return $addOutgo;


}