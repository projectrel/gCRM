<?php
include_once '../funcs.php';
include_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';
if (!isset($_SESSION))
    session_start();
$branch_id = $_SESSION['branch_id'];
$user_id = $_SESSION['id'];

switch (accessLevel()) {
    case 3:
    case 2:
    $info = $connection->query("
        SELECT FT.fiat_transfer_id AS id, concat(U.last_name, ' ', U.first_name) AS `агент`, MOO1.method_name AS `счет отправитель`, concat(`transfer_sum_from`, ' ', F1.full_name) AS `отправлено`, MOO2.method_name AS `счет получатель`,
        concat(`transfer_sum_to`, ' ', F2.full_name) AS `получено`, IFNULL(MAX(LC.change_date),'-') AS `пос. редакт.`
        FROM `fiat_transfers` FT
        INNER JOIN `methods_of_obtaining` MOO1 ON MOO1.method_id = FT.method_from_id
        INNER JOIN `methods_of_obtaining` MOO2 ON MOO2.method_id = FT.method_to_id
        INNER JOIN `payments` P1 ON MOO1.method_id = P1.method_id
        INNER JOIN `payments` P2 ON MOO2.method_id = P2.method_id
        INNER JOIN `fiats` F1 ON F1.fiat_id = P1.fiat_id
        INNER JOIN `fiats` F2 ON F2.fiat_id = P2.fiat_id
        LEFT JOIN `changes`LC ON FT.fiat_transfer_id = LC.fiat_transfer_id
        INNER JOIN `users` U ON U.user_id = FT.user_id
        WHERE U.branch_id = '$branch_id'
        GROUP BY FT.fiat_transfer_id
");
        break;
    case 1:
        $info = $connection->query("
        SELECT `fiat_transfer_id` AS id, MOO1.method_name AS `счет отправитель`, concat(`transfer_sum_from`, ' ', F1.full_name) AS `отправлено`, MOO2.method_name AS `счет получатель`,
        concat(`transfer_sum_to`, ' ', F2.full_name) AS `получено`  
        FROM `fiat_transfers` FT
        INNER JOIN `methods_of_obtaining` MOO1 ON MOO1.method_id = FT.method_from_id
        INNER JOIN `methods_of_obtaining` MOO2 ON MOO2.method_id = FT.method_to_id
        INNER JOIN `payments` P1 ON MOO1.method_id = P1.method_id
        INNER JOIN `payments` P2 ON MOO2.method_id = P2.method_id
        INNER JOIN `fiats` F1 ON F1.fiat_id = P1.fiat_id
        INNER JOIN `fiats` F2 ON F2.fiat_id = P2.fiat_id
        INNER JOIN `users` U ON U.user_id = FT.user_id
        WHERE U.user_id = '$user_id'
");
        break;
    default:
        exit();
        break;
}
$data['methods'] = $connection->query("
SELECT MOO.method_id, concat(MOO.method_name,'(',F.full_name,')') AS `method_name` FROM `methods_of_obtaining` MOO 
INNER JOIN payments P ON MOO.method_id = P.method_id
INNER JOIN fiats F ON P.fiat_id = F.fiat_id
WHERE `branch_id` = '$branch_id'");


$options['type'] = 'Transfer';
$options['text'] = 'История переводов';
$options['btn'] = 100;
$options['btn-max'] = 2;
$options['btn-text'] = 'Добавить';
$options['edit'] = 2;
echo template(display_data($info, $options, $data));
