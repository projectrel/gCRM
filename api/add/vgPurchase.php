<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/funcs.php";
include_once("../../db.php");

if (!isset($_POST['vg_id'], $_POST['vg_sum'], $_POST['on_credit'], $_POST['fiat_id'])) {
    return error("empty");
}
session_start();
$user_id = $_SESSION['id'];
$sum_vg = clean($_POST['vg_sum']);
$vg_id = clean($_POST['vg_id']);
$fiat_id = clean($_POST['fiat_id']);
$on_credit = clean($_POST['on_credit']) === "true" ? 1 : 0;
$sum_currency = mysqli_fetch_assoc($connection->query("SELECT in_percent FROM vg_data WHERE vg_data_id = '$vg_id'"))['in_percent'] / 100 * $sum_vg;

if (!addPurchase($connection, $user_id, $vg_id, $sum_vg, $sum_currency, $fiat_id, $on_credit)) {
    return error("failed");
}

echo json_encode(array("status" => "success"));


function addPurchase($connection, $user_id, $vg_id, $sum_vg, $sum_currency, $fiat_id, $on_credit)
{
    $vg_purchase_credit = $on_credit ? $sum_vg : 0;
    $add_vg_purchase = ($connection->query("
           INSERT INTO `vg_purchases`
           (`user_id`, `vg_data_id`, `fiat_id`, `vg_purchase_sum`,`vg_purchase_sum_currency`, `vg_purchase_credit`, `vg_purchase_on_credit`) 
           VALUES 
           ('$user_id','$vg_id','$fiat_id','$sum_vg','$sum_currency','$vg_purchase_credit','$on_credit')"));
    return $add_vg_purchase;
}

function updateBalance()
{

}

function addOutgo()
{

}