<?php
if (isset($_POST['vg_id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $vg_id = clean($_POST['vg_id']);
    session_start();
    $vg_data = mysqli_fetch_assoc($connection->query("
    SELECT VG.vg_data_id AS `id`, VG.name AS `name`, in_percent As `in`, out_percent AS `out`, api_url_regexp AS `url`, access_key AS `key`
    FROM  vg_data VG 
    INNER JOIN branch B ON B.branch_id = VG.branch_id
    WHERE B.branch_id = " . $_SESSION['branch_id'] . "
    AND VG.vg_data_id = '$vg_id'
    "));

    if ($vg_data) {
        echo json_encode($vg_data);
        return false;
    } else {
        error("failed");
        return false;
    }
}
