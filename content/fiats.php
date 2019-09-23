<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';


$options['type'] = 'Fiat';
$options['text'] = 'Фиатные валюты';
$options['edit'] = 3;
$options['btn'] = 3;
session_start();
//$options['btn-max'] = 2;
$options['btn-text'] = 'Добавить';


if(iCan(3)){ // currently
    echo template(display_data($connection -> query('
    SELECT fiat_id AS `id`, code AS "код", full_name AS "название", name AS "сокращение"
    FROM fiats
'), $options));
}

