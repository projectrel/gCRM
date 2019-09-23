<?php

if (!isset($_GET['login'], $_GET['vgSum'])) {
    echo array('status' => 'failed', 'error' => 'empty');
    return false;
}

include_once '../../db.php';
include_once './funcs.php';
$login = clean($_GET['login']);
$vgsum = clean($_GET['vgSum']);

$client = mysqli_fetch_assoc($connection->query("
    SELECT * 
    FROM clients
    WHERE login = '$login'
"));

if (!$client) {
    echo json_encode(array("error" => "not exists"));
    return;
}

$vg = mysqli_fetch_assoc($connection->query("
    SELECT DISTINCT *
    FROM vg_data D
    INNER JOIN virtualgood V ON V.vg_id = D.vg_id
    INNER JOIN(
        SELECT vg_id, real_out_percent AS outp, fiat_id
        FROM orders
        WHERE client_id IN (
            SELECT client_id
            FROM clients
            WHERE login = '$login'
        ) AND `date` IN (
            SELECT MAX(`date`)
            FROM orders
        WHERE client_id IN (
            SELECT client_id
            FROM clients
            WHERE login = '$login'
        ) 
    )
    ) T ON T.vg_id = D.vg_id
"));
$vg_name = $vg['name'];
$vg_perc = $vg['outp'];
$fiat_id = $vg['fiat_id'];
session_start();
$_SESSION['vg_id'] = $vg['vg_id'];
$_SESSION['fiat_id'] = $fiat_id;

$fiatName = mysqli_fetch_assoc($connection->query("SELECT * FROM fiats WHERE fiat_id = '$fiat_id'"))['name'];

$client_id = $client['client_id'];
$canPayInDebt = $client['pay_in_debt'];
$canPay = $client['payment_system'];
$paypage = $client['pay_page'];
if (!$paypage) {
    echo json_encode(array("error" => "deal denied"));
    return;
}
$debtLimit = $client['max_debt'] - mysqli_fetch_assoc($connection->query("
    SELECT SUM(sum) AS sum
    FROM payments
    WHERE client_debt_id = '$client_id'
"))['sum'];
$debtLimit = $debtLimit < 0 ? 0 : $debtLimit;
$debtLimit = $canPayInDebt ? $debtLimit : 0;
$sum = (int)$vg_perc * (int)$vgsum / 100;

echo json_encode(array("debtLimit" => $debtLimit, "pay_page" => $paypage, "fiatName" => $fiatName, "paySystem" => $canPay, "vgName" => $vg_name, "sum" => $sum));