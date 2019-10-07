<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';

$options['type'] = 'Branch';
$options['text'] = 'Предприятия';
$options['btn-text'] = 'Добавить';
$options['btn'] = 3;
$options['edit'] = 3;
echo template(display_data($connection -> query('
SELECT  B.branch_id AS `id`, ik_id AS `Interkassa ID`, branch_name AS `название`, `active` AS `статус`, IFNULL(MAX(LC.change_date),"-") AS "пос. редакт."
FROM branch B LEFT OUTER JOIN changes LC ON B.branch_id = LC.branch_id
GROUP BY B.branch_id'), $options));

