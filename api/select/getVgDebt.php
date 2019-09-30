<?php
include_once("../../db.php");
include_once("../../funcs.php");
if (!isset($_GET['fiat_id'], $_GET['vg_id']))
    error("empty");
$fiat_id = clean($_GET['fiat_id']);
$vg_id = clean($_GET['vg_id']);
$res = mysqli_fetch_assoc($connection->
query("SELECT SUM(vg_purchase_credit) AS `sum` FROM `vg_purchases` 
              WHERE vg_data_id = '$vg_id' AND fiat_id = '$fiat_id'
              GROUP BY vg_data_id
              
"));
if ($res) {
    echo json_encode($res);
    return false;
}
error("failed");
