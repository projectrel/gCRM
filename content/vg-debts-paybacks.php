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
if(!isset($_SESSION))
    session_start();
//$options['btn-max'] = 2;
$options['btn-text'] = 'Выплатить';
$branch_id = $_SESSION['branch_id'];

$outgo_vg_purchase_type = VG_PURCHASE_TYPE;

$data['vgs'] = $connection->query("
    SELECT * FROM vg_data WHERE  branch_id = " . $_SESSION['branch_id'] . "
");
$data['methods'] = $connection->query("
SELECT MOO.method_id, concat(MOO.method_name,'(',F.full_name,')') AS `method_name` FROM `methods_of_obtaining` MOO 
INNER JOIN payments P ON MOO.method_id = P.method_id
INNER JOIN fiats F ON P.fiat_id = F.fiat_id
WHERE `branch_id` = '$branch_id'");

echo template(display_data($connection->query("
SELECT O.outgo_id AS id, VD.name AS `vg`, O.sum AS `сумма`, MOO.method_name AS 'метод оплаты', F.full_name AS 'валюта', O.date AS `дата`, IFNULL(MAX(LC.change_date),'-') AS 'пос. редакт.'
FROM outgo O
INNER JOIN vg_data VD ON VD.vg_data_id = O.vg_data_id
INNER JOIN methods_of_obtaining MOO ON MOO.method_id = O.method_id
INNER JOIN payments P ON P.method_id = MOO.method_id
INNER JOIN fiats F ON F.fiat_id = P.fiat_id
LEFT OUTER JOIN changes LC ON O.outgo_id = LC.outgo_id
WHERE O.outgo_type_id = '$outgo_vg_purchase_type' AND O.branch_id = " . $_SESSION['branch_id'] . "
GROUP BY O.outgo_id
ORDER BY O.date DESC
"), $options, $data));

