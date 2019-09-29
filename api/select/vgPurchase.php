<?php
if (isset($_POST['vg_purchase_id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $vg_purchase_id = clean($_POST['vg_purchase_id']);
    session_start();
    $vg_data = mysqli_fetch_assoc($connection->query("
    SELECT vg_data_id, vg_purchase_id, fiat_id, vg_purchase_on_credit, vg_purchase_sum
    FROM  vg_purchases
    WHERE vg_purchase_id = '$vg_purchase_id'"));

    if ($vg_data) {
        echo json_encode($vg_data);
        return false;
    } else {
        error("failed");
        return false;
    }
}

