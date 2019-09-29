<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/funcs.php";
include_once("../../db.php");
if (!isset($_POST['vg_id'], $_POST['vg_purchase_id'], $_POST['vg_sum'], $_POST['on_credit'], $_POST['fiat_id'])) {
    return error("empty");
}
session_start();
$vg_purchase_id = clean($_POST['vg_purchase_id']);
$branch_id = $_SESSION['branch_id'];
$user_id = $_SESSION['id'];
$sum_vg = clean($_POST['vg_sum']);
$vg_id = clean($_POST['vg_id']);
$fiat_id = clean($_POST['fiat_id']);
$on_credit = clean($_POST['on_credit']) === "true" ? 1 : 0;
$sum_currency = mysqli_fetch_assoc($connection->query("SELECT in_percent FROM vg_data WHERE vg_data_id = '$vg_id'"))['in_percent'] / 100 * $sum_vg;


if (!updateOutgo($connection, $vg_purchase_id, $user_id, $fiat_id, $sum_currency, $on_credit)) {
    return error("failed");
}

if (!updateBranchBalance($connection, $vg_purchase_id, $on_credit, $branch_id, $fiat_id, $sum_currency)) {
    return error("failed");
}

if (!editVGBalance($connection, $vg_purchase_id, $vg_id, $sum_vg)) {
    return error("failed");
}

if (!editPurchase($connection, $vg_purchase_id, $vg_id, $sum_vg, $sum_currency, $fiat_id, $on_credit)) {
    return error("failed");
}
echo json_encode(array("status" => "success"));


function editPurchase($connection, $vg_purchase_id, $vg_id, $sum_vg, $sum_currency, $fiat_id, $on_credit)
{
    $vg_purchase_credit = $on_credit ? $sum_vg : 0;
    $edit_vg_purchase = ($connection->query("
          UPDATE `vg_purchases` SET `vg_data_id`='$vg_id',
          `fiat_id`='$fiat_id',`vg_purchase_sum`='$sum_vg',
          `vg_purchase_sum_currency`='$sum_currency',`vg_purchase_credit`='$vg_purchase_credit',
          `vg_purchase_on_credit`='$on_credit' WHERE `vg_purchase_id` = '$vg_purchase_id'"));
    return $edit_vg_purchase;
}

function editVGBalance($connection, $vg_purchase_id, $vg_id, $sum_vg)
{
    $old_sum = mysqli_fetch_assoc($connection->query("SELECT vg_purchase_sum FROM `vg_purchases` WHERE `vg_purchase_sum` = '$vg_purchase_id'"))['vg_purchase_sum'];
    $edit_balance = ($connection->query("
        UPDATE vg_data SET `vg_amount` = `vg_amount` + '$sum_vg' - '$old_sum' WHERE `vg_data_id` = '$vg_id'"));
    return $edit_balance;
}

function updateBranchBalance($connection, $vg_purchase_id, $on_credit, $branch_id, $fiat_id, $sum_currency)
{
    $update_balance = true;
    $old_on_credit = mysqli_fetch_assoc($connection->
    query("SELECT vg_purchase_on_credit 
           FROM `vg_purchases` WHERE `vg_purchase_sum` = '$vg_purchase_id'"))['vg_purchase_on_credit'];
    if (!$old_on_credit) {
        $old_sum_currency = mysqli_fetch_assoc($connection->
        query("SELECT vg_purchase_sum_currency 
               FROM `vg_purchases` WHERE `vg_purchase_sum` = '$vg_purchase_id'"))['vg_purchase_sum_currency'];
        if (!$on_credit) {
            $update_balance = ($connection->query("
        UPDATE payments SET `sum` = `sum` - '$sum_currency' + '$old_sum_currency' WHERE `branch_id` = '$branch_id' AND `fiat_id` = '$fiat_id'"));
        } else {
            $update_balance = ($connection->query("
        UPDATE payments SET `sum` = `sum` + '$old_sum_currency' WHERE `branch_id` = '$branch_id' AND `fiat_id` = '$fiat_id'"));
        }
    } else {
        if (!$on_credit) {
            $update_balance = ($connection->query("
        UPDATE payments SET `sum` = `sum` - '$sum_currency' WHERE `branch_id` = '$branch_id' AND `fiat_id` = '$fiat_id'"));
        }
    }

    return $update_balance;
}

function updateOutgo($connection, $vg_purchase_id, $user_id, $fiat_id, $sum, $on_credit)
{
    include_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";
    $vg_purchase_type = VG_PURCHASE_TYPE;
    $editOutgo = true;
    $old_on_credit = mysqli_fetch_assoc($connection->
    query("SELECT vg_purchase_on_credit 
           FROM `vg_purchases` WHERE `vg_purchase_sum` = '$vg_purchase_id'"))['vg_purchase_on_credit'];
    if (!$old_on_credit) {
        if (!$on_credit) {
            $editOutgo = ($connection->query("
           UPDATE `outgo` SET `sum` = '$sum' WHERE vg_purchase_id = '$vg_purchase_id'"));
        } else {
            $editOutgo = ($connection->query("
           DELETE FROM `outgo` WHERE vg_purchase_id = '$vg_purchase_id'"));
        }
    } else {
        if (!$on_credit) {
            $editOutgo = ($connection->query("
           INSERT INTO `outgo`
           (`user_id`, `fiat_id`, `outgo_type_id`, `date`, `sum`, `vg_purchase_id`) 
           VALUES 
           ('$user_id','$fiat_id','$vg_purchase_type',now(),'$sum', '$vg_purchase_id' )"));
        }
    }

    return $editOutgo;


}