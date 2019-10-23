<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/funcs.php";
include_once("../../db.php");
if (!isset($_POST['vg_id'], $_POST['vg_purchase_id'], $_POST['vg_sum'], $_POST['fiat_id'])) {
    return error("empty");
}
if(!isset($_SESSION))
    session_start();
$vg_purchase_id = clean($_POST['vg_purchase_id']);
$branch_id = $_SESSION['branch_id'];
$user_id = $_SESSION['id'];
$sum_vg = clean($_POST['vg_sum']);
$vg_id = clean($_POST['vg_id']);
$fiat_id = clean($_POST['fiat_id']);
$sum_currency = mysqli_fetch_assoc($connection->query("SELECT in_percent FROM vg_data WHERE vg_data_id = '$vg_id'"))['in_percent'] / 100 * $sum_vg;

//if (!updateOutgo($connection, $vg_id, $vg_purchase_id, $user_id, $fiat_id, $sum_currency)) {
//    return error("failed");
//}

if (!updateDebtBalance($connection, $vg_purchase_id, $vg_id, $fiat_id, $sum_currency)) {
    return error("failed");
}

if (!editVGBalance($connection, $vg_purchase_id, $vg_id, $sum_vg)) {
    return error("failed");
}

if (!editPurchase($connection, $vg_purchase_id, $vg_id, $sum_vg, $sum_currency, $fiat_id)) {
    return error("failed");
}

if (!save_change_info($connection,'vg_purchase',$vg_purchase_id)) {
    return error("failed");
}
echo json_encode(array("status" => "success"));


function editPurchase($connection, $vg_purchase_id, $vg_id, $sum_vg, $sum_currency, $fiat_id)
{
    $vg_purchase_credit = $sum_currency;
    $edit_vg_purchase = ($connection->query("
          UPDATE `vg_purchases` SET `vg_data_id`='$vg_id',
          `fiat_id`='$fiat_id',`vg_purchase_sum`='$sum_vg',
          `vg_purchase_sum_currency`='$sum_currency',`vg_purchase_credit`='$vg_purchase_credit',
          `vg_purchase_on_credit`= 1 WHERE `vg_purchase_id` = '$vg_purchase_id'"));
    return $edit_vg_purchase;
}

function editVGBalance($connection, $vg_purchase_id, $vg_id, $sum_vg)
{
    $old_purchase = mysqli_fetch_assoc($connection->
    query("SELECT vg_purchase_sum, vg_data_id
           FROM `vg_purchases` 
           WHERE `vg_purchase_id` = '$vg_purchase_id'"));
    $old_sum = $old_purchase['vg_purchase_sum'];
    $old_vg_data_id = $old_purchase['vg_data_id'];
    $edit_balance1 = ($connection->query("
        UPDATE vg_data SET `vg_amount` = `vg_amount` - '$old_sum' WHERE `vg_data_id` = '$old_vg_data_id'"));

    $edit_balance2 = ($connection->query("
        UPDATE vg_data SET `vg_amount` = `vg_amount` + '$sum_vg'  WHERE `vg_data_id` = '$vg_id'"));
    return ($edit_balance1 && $edit_balance2);
}

function updateDebtBalance($connection, $vg_purchase_id, $vg_id, $fiat_id, $sum_currency)
{
    $old_purchase_data = mysqli_fetch_assoc($connection->
    query("SELECT vg_purchase_sum_currency, fiat_id, vg_data_id
           FROM `vg_purchases` WHERE `vg_purchase_id` = '$vg_purchase_id'"));
    $old_sum_currency = $old_purchase_data['vg_purchase_sum_currency'];
    $old_fiat_id = $old_purchase_data['fiat_id'];
    $old_vg_data_id = $old_purchase_data['vg_data_id'];
    return updateDebtBalanceSub($connection, $old_vg_data_id, $vg_id, $old_fiat_id, $fiat_id, $sum_currency, $old_sum_currency);
}


function updateDebtBalanceSub($connection, $old_vg_data_id, $vg_id_data_id, $old_fiat_id, $fiat_id, $sum_currency, $old_sum_currency)
{
    if ($old_fiat_id == $fiat_id && $old_vg_data_id == $vg_id_data_id) {
        $payment = mysqli_fetch_assoc($connection->query("
         SELECT * FROM payments WHERE fiat_id = '$fiat_id' AND vg_data_debt_id = '$vg_id_data_id'"));
        $payment_id = $payment['payment_id'];
        $update_debt_balance = $connection->query("
         UPDATE payments SET `sum` = `sum` + '$sum_currency' - '$old_sum_currency' WHERE payment_id = '$payment_id'");
        $update_debt_balance_old = true;
    } else {
        $payment_is_exists = mysqli_fetch_assoc($connection->query("
         SELECT * FROM payments WHERE fiat_id = '$fiat_id' AND vg_data_debt_id = '$vg_id_data_id'"));

        $payment_id_old = mysqli_fetch_assoc($connection->query("
         SELECT * FROM payments WHERE fiat_id = '$old_fiat_id' AND vg_data_debt_id = '$old_vg_data_id'"))['payment_id'];

        $update_debt_balance_old = $connection->query("
        UPDATE payments SET `sum` = `sum` - '$old_sum_currency' WHERE payment_id = '$payment_id_old'");

        if (count($payment_is_exists)) {
            $payment_id = $payment_is_exists['payment_id'];

            $update_debt_balance = $connection->query("
        UPDATE payments SET `sum` = `sum` + '$sum_currency'  WHERE payment_id = '$payment_id'");

        } else {

            $update_debt_balance = $connection->query("
        INSERT INTO payments (`fiat_id`, `sum`, `vg_data_debt_id`) VALUES ('$fiat_id', '$sum_currency', '$vg_id_data_id')");
        }
    }
    return $update_debt_balance && $update_debt_balance_old;
}























//function updateOutgo($connection, $vg_id, $vg_purchase_id, $user_id, $fiat_id, $sum, $on_credit)
//{
//    include_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";
//    $vg_purchase_type = VG_PURCHASE_TYPE;
//    $editOutgo = true;
//    $old_on_credit = mysqli_fetch_assoc($connection->
//    query("SELECT vg_purchase_on_credit
//           FROM `vg_purchases` WHERE `vg_purchase_id` = '$vg_purchase_id'"))['vg_purchase_on_credit'];
//    if (!$old_on_credit) {
//        if (!$on_credit) {
//            $editOutgo = ($connection->query("
//           UPDATE `outgo` SET `sum` = '$sum', `fiat_id` = '$fiat_id' WHERE vg_data_id = '$vg_id'"));
//        } else {
//
////            $editOutgo = ($connection->query("
////           DELETE FROM `outgo` WHERE vg_data_id = '$vg_id'"));
//        }
//    } else {
//        if (!$on_credit) {
//            $editOutgo = ($connection->query("
//           INSERT INTO `outgo`
//           (`user_id`, `fiat_id`, `outgo_type_id`, `date`, `sum`, `vg_data_id`)
//           VALUES
//           ('$user_id','$fiat_id','$vg_purchase_type',now(),'$sum', '$vg_id' )"));
//        }
//    }
//
//    return $editOutgo;
//
//
//}