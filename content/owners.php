<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';

$users = $connection->query('
SELECT *
FROM users
WHERE is_owner = 0
');
if(iCan(3)){
    $branches = $connection->query('
SELECT branch_id AS `id`, branch_name
FROM branch
');
    $table = $connection -> query('
        SELECT owner_id AS `id`, concat(O.last_name, " ", O.first_name) AS  Имя, branch_name AS `предприятие`
        FROM (SELECT user_id AS `owner_id`, first_name, last_name, branch_id FROM users WHERE is_owner = 1) O
        INNER JOIN branch B ON B.branch_id = O.branch_id
    ');
}else{
    $users = $connection->query('
SELECT *
FROM users
WHERE branch_id = '.$_SESSION['branch_id'].' AND is_owner = 0 AND role != "moder"
');
    $branches = $connection->query('
SELECT branch_id AS `id`, branch_name
FROM branch
WHERE branch_id = '.$_SESSION['branch_id'].'
');
    $table = $connection -> query('
        SELECT owner_id AS `id`, concat(O.last_name, " ", O.first_name) AS  Имя, branch_name AS `предприятие`
        FROM (SELECT user_id AS `owner_id`, first_name, last_name, branch_id FROM users WHERE is_owner = 1) O
        INNER JOIN branch B ON B.branch_id = O.branch_id
        WHERE B.branch_id = '.$_SESSION['branch_id'].'
    ');
}

$data['branches'] = $branches;
$data['users'] = $users;
$options['minus'] = true;
$options['type'] = 'Owner';
$options['text'] = 'Владельцы';
$options['btn-text'] = 'Добавить';
$options['btn'] = 2;
echo template(display_data($table, $options, $data));

