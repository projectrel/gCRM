<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';


$options['type'] = 'VGDebt';
$options['text'] = 'Задолженность по VG';
$options['edit'] = 0;
$options['btn'] = 1;
session_start();
//$options['btn-max'] = 2;
$options['btn-text'] = 'Выплатить';

$data['vgs'] = $connection->query("
    SELECT VD.vg_data_id, VD.name FROM vg_data VD 
    INNER JOIN vg_purchases VP ON VD.vg_data_id = VP.vg_data_id 
    INNER JOIN payments P ON P.vg_data_debt_id = VP.vg_data_id
    WHERE  VD.branch_id = " . $_SESSION['branch_id'] . "
    GROUP BY VD.vg_data_id, VD.name
    HAVING SUM(P.sum) > 0
");
$data['fiats'] = $connection->query("
    SELECT F.fiat_id, F.full_name FROM fiats F INNER JOIN vg_purchases VP ON F.fiat_id = VP.fiat_id INNER JOIN vg_data VD ON VD.vg_data_id = VP.vg_data_id
    WHERE  branch_id = " . $_SESSION['branch_id'] . "
    GROUP BY F.fiat_id
    HAVING SUM(VP.vg_purchase_credit) > 0
");
echo template(display_data($connection->query('
SELECT VD.name AS `VG`, P.sum AS `сумма задолженности`, F.full_name AS `валюта`
FROM vg_data VD 
INNER JOIN payments P ON P.vg_data_debt_id = VD.vg_data_id
INNER JOIN fiats F ON F.fiat_id = P.fiat_id
WHERE  P.sum > 0 AND VD.branch_id = ' . $_SESSION['branch_id'] . '
'), $options, $data));

