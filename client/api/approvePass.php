<?php

if (!isset($_GET['login'], $_GET['password'])) {
    echo array('status' => 'failed', 'error' => 'empty');
    return false;
}
include_once '../../db.php';
include_once './funcs.php';
$login = clean($_GET['login']);
$password = clean($_GET['password']);

$exists = mysqli_fetch_assoc($connection->query(" SELECT * FROM clients WHERE login = '$login' AND `password` = '$password'"));
$client_id = $exists['client_id'];
$nvgs = array();
$vgs = mysqliToArray($connection->query("
    SELECT DISTINCT V.vg_id, V.name
    FROM vg_data D
    INNER JOIN virtualgood V ON V.vg_id = D.vg_id
    WHERE D.vg_id IN (
        SELECT vg_id FROM orders WHERE client_id = '$client_id'
    )
"));

foreach ($vgs as $vg) {
    $vg_id = $vg['vg_id'];
    $vg['out_percent'] = mysqli_fetch_assoc($connection->query("SELECT * FROM orders WHERE client_id = '$client_id' AND vg_id = '$vg_id'"))['real_out_percent'];
    array_push($nvgs, $vg);
}

if ($exists) {
    $_SESSION['client_id'] = $exists['client_id'];
    echo json_encode(array("status" => "success", "vgs" => $nvgs));
} else {
    echo json_encode(array("status" => "failed", "error" => "wrong params"));
}
