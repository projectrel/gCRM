<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';


$options['type'] = 'globalVG';
$options['text'] = 'Глобальные VG';
$options['edit'] = 3;
$options['btn'] = 3;
$options['btn-text'] = 'Добавить';

$options2['type'] = 'vgGlobalStats';
$options2['text'] = 'Статистика';
$options2['edit'] = 13;
$options2['btn'] = 13;
$options2['btn-text'] = 'Добавить';
session_start();



echo template(display_data($connection->query('
SELECT VG.vg_id AS `id`, VG.vg_id AS `Уникальный id`, VG.name AS `название`, VG.active as `статус`
FROM `virtualgood` VG WHERE active = 1
'), $options, '') . display_data($connection->query('
SELECT VG.vg_id AS `id`, VG.name AS `название`, B.branch_name AS `предприятие`, COUNT(`vg_data_id`) AS `количество`,
ROUND((SUM(`in_percent`) / COUNT(`in_percent`)),2) AS `средний in %`, ROUND((SUM(`out_percent`) / COUNT(`out_percent`)),2) AS `средний out %`
FROM `virtualgood` VG 
INNER JOIN `vg_data` VD ON VG.vg_id = VD.vg_id 
INNER JOIN `branch` B ON VD.branch_id = B.branch_id 
GROUP BY VG.vg_id, VG.name, B.branch_name
'), $options2, ''));

