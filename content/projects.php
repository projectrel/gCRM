<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';


$options['type'] = 'Project';
$options['text'] = 'Проекты';
$options['edit'] = 1;
$options['btn'] = 1;
if(!isset($_SESSION))
    session_start();
//$options['btn-max'] = 2;
$options['btn-text'] = 'Добавить';

$branch_id = $_SESSION['branch_id'];

echo template(display_data($connection->query('
    SELECT P.project_id AS `id`, `project_name` AS "название", `active` AS "статус", IFNULL(MAX(LC.change_date),"-") AS "пос. редакт."
    FROM projects P LEFT OUTER JOIN changes LC ON P.project_id = LC.project_id
WHERE P.branch_id = "'.$branch_id.'"
GROUP BY P.project_id
'), $options));

