<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';

session_start();
$branch_id = $_SESSION['branch_id'];
$options['type'] = 'MethodsOfObtaining';
$options['text'] = 'Методы оплаты';
$options['edit'] = 2;
$options['btn'] = 2;
$options['btn-text'] = 'Добавить';
switch (accessLevel()) {
    case 3:
        $data = '
SELECT method_id AS `id`, method_name AS `название`, branch_name AS `предприятие`, MOO.is_active AS `статус`, method_name AS `название`, participates_in_balance AS `участие в балансе`
FROM `methods_of_obtaining` MOO
INNER JOIN `branch` B ON MOO.branch_id = B.branch_id 
';
        break;
    case 1:
    case 2:
        $data = "
SELECT method_id AS `id`, method_name AS `название`, is_active AS `статус`, method_name AS `название`, participates_in_balance AS `участие в балансе`
FROM `methods_of_obtaining` WHERE branch_id = '$branch_id'
";
        break;

}


echo template(display_data($connection->query($data), $options));

