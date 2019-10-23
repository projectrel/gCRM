<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';
if(!isset($_SESSION))
    session_start();
$branch_id = $_SESSION['branch_id'];
switch (accessLevel()) {
    case 3:
        $res = ($connection->query('
SELECT U.user_id AS `id`, concat(last_name, " ", first_name) AS `Имя`, role AS `должность`, branch_name AS `отделение`, telegram AS Телеграм, U.email AS `email`, U.active AS `статус`, IFNULL(MAX(LC.change_date),"-") AS "пос. редакт."
FROM users U
INNER JOIN branch B ON B.branch_id = U.branch_id 
LEFT OUTER JOIN changes LC ON U.user_id = LC.user_id
GROUP BY U.user_id
'));
        break;
    case 2:
        $res = ($connection->query("
SELECT U.user_id AS `id`, concat(last_name, ' ', first_name) AS `Имя`, role AS `должность`, branch_name AS `отделение`, telegram AS Телеграм, U.email AS `email`, U.active AS `статус`, IFNULL(MAX(LC.change_date),\"-\") AS \"пос. редакт.\"
FROM users U
INNER JOIN branch B ON B.branch_id = U.branch_id
WHERE B.branch_id = '$branch_id' AND U.role != 'moder'
LEFT OUTER JOIN changes LC ON U.user_id = LC.user_id
GROUP BY U.user_id
"));
        break;
    case 1:
        $res = ($connection->query("
SELECT user_id AS `id`, concat(last_name, ' ', first_name) AS `Имя`, branch_name AS `отделение`, telegram AS Телеграм, U.email AS `email`, U.active AS `статус`
FROM users U
INNER JOIN branch B ON B.branch_id = U.branch_id
WHERE B.branch_id = '$branch_id' AND U.role != 'moder'
"));
        break;
    default:
        exit();
        break;
}
$options['type'] = 'User';
$options['text'] = 'Сотрудники';
$options['edit'] = 2;
$options['btn-text'] = 'Добавить';
$options['btn'] = 2;
$options['printbtn'] = true;
if(iCan(3)){
    $branches = $connection->query('
SELECT * FROM branch
');
}else if(iCan(2)){
    $branches = $connection->query('
SELECT * FROM branch WHERE branch_id = '.$_SESSION['branch_id'].'
');
}
echo template(display_data($res, $options, $branches));
?>