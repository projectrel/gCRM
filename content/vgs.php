<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';


$options['type'] = 'VG';
$options['text'] = 'VG';
$options['edit'] = 2;
$options['btn'] = 2;
if(!isset($_SESSION))
    session_start();
//$options['btn-max'] = 2;
$options['btn-text'] = 'Добавить';

$data['vgs'] = $connection->query("
    SELECT * FROM virtualgood WHERE active=1
");
    echo template(display_data($connection -> query('
SELECT VD.vg_data_id AS `id`, name AS `название`, vg_amount AS `количество`, vg_api_amount AS `кол-во на api`,
 in_percent AS "покупка %", out_percent AS "продажа %", 
api_url_regexp AS "ссылка-шаблон", access_key AS "ключ доступа", 
IFNULL(MAX(LC.change_date),"-") AS "пос. редакт."
FROM vg_data VD LEFT OUTER JOIN changes LC ON VD.vg_data_id = LC.vg_data_id
WHERE VD.branch_id = '.$_SESSION['branch_id'].'
GROUP BY VD.vg_data_id
'), $options, $data));

