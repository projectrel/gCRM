<?php
if (isset($_POST['vg_id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $vg_id = clean($_POST['vg_id']);
    $vg_data = mysqli_fetch_assoc($connection->query("
    SELECT `name` 
    FROM virtualgood VG
    WHERE  VG.vg_id = '$vg_id'
    "));
    if ($vg_data) {
        echo json_encode($vg_data);
        return false;
    } else {
        return error("failed");
    }
}
