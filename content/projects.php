<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';


$options['type'] = 'Project';
$options['text'] = 'Проекты';
$options['edit'] = 1;
$options['btn'] = 1;
session_start();
//$options['btn-max'] = 2;
$options['btn-text'] = 'Добавить';

$branch_id = $_SESSION['branch_id'];

echo template(display_data($connection->query('
    SELECT project_id AS `id`, `project_name` AS "название", `active` AS "статус"
    FROM projects
WHERE branch_id = "'.$branch_id.'"
'), $options));

