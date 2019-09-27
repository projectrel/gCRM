<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';


$options['type'] = 'VG-PURCHASE';
$options['text'] = 'VG-PURCHASE';
$options['edit'] = 2;
$options['btn'] = 2;
session_start();
//$options['btn-max'] = 2;
$options['btn-text'] = 'Добавить';

$data['vg-purchases'] = $connection->query("
    SELECT * FROM virtualgood WHERE active=1
");
echo template(display_data($connection -> query('
SELECT VP.vg_purchase_id AS `id`, VP.vg_purchase_id AS `номер закупки`,  concat(U.last_name, " ", U.first_name) AS `агент`, VD.name AS `vg`, F.name AS `валюта`, VP.vg_purchase_sum AS `сума`, 
VP.vg_purchase_credit AS `долг`, VP.vg_purchase_on_credit AS `куплено в долг`
FROM vg_purchases VP INNER JOIN users U ON VP.user_id = U.user_id INNER JOIN fiats F ON VP.fiat_id = F.fiat_id
INNER JOIN vg_data VD ON VP.vg_data_id = VD.vg_data_id
WHERE U.branch_id = '.$_SESSION['branch_id'].'
'), $options, $data));

