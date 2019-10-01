<?php
if (isset($_GET['outgo_id'])) {
    include_once("../../../db.php");
    include_once("../../../funcs.php");
    $outgo_id = clean($_GET['outgo_id']);
    session_start();
    $vg_data = mysqli_fetch_assoc($connection->query("
    SELECT `outgo_id`, `vg_data_id`, `fiat_id`, `sum`
    FROM  `outgo`
    WHERE outgo_id = '$outgo_id'"));

    if ($vg_data) {
        echo json_encode($vg_data);
        return false;
    } else {
        error("failed");
        return false;
    }
}

