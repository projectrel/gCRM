<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';


$options['type'] = 'VG';
$options['text'] = 'VG';
$options['edit'] = 2;
$options['btn'] = 2;
session_start();
//$options['btn-max'] = 2;
$options['btn-text'] = 'Добавить';

$data['vgs'] = $connection->query("
    SELECT * FROM virtualgood WHERE active=1
");
    echo template(display_data($connection -> query('
SELECT vg_data_id AS `id`, name AS `название`, vg_amount AS `количество`, vg_api_amount AS `кол-во на api`,
 in_percent AS "покупка %", out_percent AS "продажа %", 
api_url_regexp AS "ссылка-шаблон", access_key AS "ключ доступа"
FROM vg_data 
WHERE branch_id = '.$_SESSION['branch_id'].'
'), $options, $data));

