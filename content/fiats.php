<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';


$options['type'] = 'Fiat';
$options['text'] = 'Фиатные валюты';
$options['edit'] = 3;
$options['btn'] = 3;
if(!isset($_SESSION))
    session_start();
//$options['btn-max'] = 2;
$options['btn-text'] = 'Добавить';


if(iCan(3)){ // currently
    echo template(display_data($connection -> query('
    SELECT F.fiat_id AS `id`, code AS "код", full_name AS "название", `name` AS "сокращение", IFNULL(MAX(LC.change_date),"-") AS "пос. редакт."
    FROM fiats F 
    LEFT JOIN changes LC ON F.fiat_id = LC.fiat_id 
    GROUP BY F.fiat_id
'), $options));
}

