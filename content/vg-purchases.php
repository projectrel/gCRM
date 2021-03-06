<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';


$options['type'] = 'VGPurchase';
$options['text'] = 'Закупки VG';
$options['edit'] = 1;
$options['btn'] = 1;
if(!isset($_SESSION))
    session_start();
//$options['btn-max'] = 2;
$options['btn-text'] = 'Закупить';

$data['vgs'] = $connection->query("
    SELECT * FROM vg_data WHERE  branch_id = " . $_SESSION['branch_id'] . "
");
$data['fiats'] = $connection->query("
    SELECT fiat_id, full_name FROM fiats
");
echo template(display_data($connection->query('
SELECT VP.date AS `дата`, VP.vg_purchase_id AS `id`, VP.vg_purchase_unique_key AS `уникальный ключ`,  concat(U.last_name, " ", U.first_name) AS `агент`,
VD.name AS `vg`, VP.vg_purchase_sum AS `сумма`,  F.full_name AS `валюта`,  concat(VP.vg_purchase_sum_currency, " ", F.name) AS `сумма в фиате`,
IFNULL(MAX(LC.change_date),"-") AS "пос. редакт."
FROM vg_purchases VP 
INNER JOIN users U ON VP.user_id = U.user_id 
INNER JOIN fiats F ON VP.fiat_id = F.fiat_id
INNER JOIN vg_data VD ON VP.vg_data_id = VD.vg_data_id
LEFT OUTER JOIN changes LC ON VP.vg_purchase_id = LC.vg_purchase_id
WHERE U.branch_id = ' . $_SESSION['branch_id'] . '
GROUP BY VP.vg_purchase_id
ORDER BY VP.date DESC
'), $options, $data));

