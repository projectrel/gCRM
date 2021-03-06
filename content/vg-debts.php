<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';


$options['type'] = 'VGDebt';
$options['text'] = 'Задолженность по VG';
$options['edit'] = 0;
$options['btn'] = 1;
if(!isset($_SESSION))
    session_start();
//$options['btn-max'] = 2;
$options['btn-text'] = 'Выплатить';

$branch_id = $_SESSION['branch_id'];

$data['vgs'] = $connection->query("
    SELECT VD.vg_data_id, VD.name FROM vg_data VD 
    INNER JOIN vg_purchases VP ON VD.vg_data_id = VP.vg_data_id 
    INNER JOIN payments P ON P.vg_data_debt_id = VP.vg_data_id
    WHERE  VD.branch_id = " . $_SESSION['branch_id'] . "
    GROUP BY VD.vg_data_id, VD.name
    HAVING SUM(P.sum) > 0
");

$data['methods'] = $connection->query("
SELECT MOO.method_id, concat(MOO.method_name,'(',F.full_name,')') AS `method_name` FROM `methods_of_obtaining` MOO 
INNER JOIN payments P ON MOO.method_id = P.method_id
INNER JOIN fiats F ON P.fiat_id = F.fiat_id
WHERE `branch_id` = '$branch_id' AND MOO.is_active = 1");

echo template(display_data($connection->query('
SELECT VD.name AS `VG`, P.sum AS `сумма задолженности`, F.full_name AS `валюта`
FROM vg_data VD 
INNER JOIN payments P ON P.vg_data_debt_id = VD.vg_data_id
INNER JOIN fiats F ON F.fiat_id = P.fiat_id
WHERE  P.sum > 0 AND VD.branch_id = ' . $_SESSION['branch_id'] . '
'), $options, $data));

