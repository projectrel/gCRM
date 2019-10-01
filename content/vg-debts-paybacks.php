<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';
include_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";


$options['type'] = 'VGPaybackDebt';
$options['text'] = 'История выплат по задолжностям';
$options['edit'] = 1;
$options['btn'] = 1000;
session_start();
//$options['btn-max'] = 2;
$options['btn-text'] = 'Выплатить';

$outgo_vg_purchase_type = VG_PURCHASE_TYPE;

$data['vgs'] = $connection->query("
    SELECT * FROM vg_data WHERE  branch_id = " . $_SESSION['branch_id'] . "
");
$data['fiats'] = $connection->query("
    SELECT fiat_id, full_name FROM fiats
");
echo template(display_data($connection->query("
SELECT O.outgo_id AS id, VD.name AS `vg`, O.sum AS `сумма`, F.full_name AS 'валюта', O.date AS `дата`
FROM outgo O
INNER JOIN vg_data VD ON VD.vg_data_id = O.vg_data_id
INNER JOIN fiats F ON F.fiat_id = O.fiat_id
WHERE O.outgo_type_id = '$outgo_vg_purchase_type' AND O.branch_id = " . $_SESSION['branch_id'] . "
ORDER BY O.date DESC
"), $options, $data));

