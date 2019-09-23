<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';
session_start();
$branch_id = $_SESSION['branch_id'];
switch (accessLevel()) {
    case 3:
        $info = $connection -> query("
SELECT B.branch_name AS `отдел`, concat(U.last_name, ' ', U.first_name) AS `агент`, U.login AS 'логин агента', concat(C.last_name, ' ', C.first_name) AS `клиент`, 
O.debt_sum AS `сумма`, F.full_name AS `валюта`,
O.date AS `дата`
FROM debt_history O
INNER JOIN clients C ON C.client_id = O.client_id 
INNER JOIN users U ON U.user_id = O.user_id
INNER JOIN branch B ON B.branch_id = U.branch_id
INNER JOIN fiats F ON F.fiat_id = O.fiat_id
ORDER BY `date` DESC
");
        break;
    case 2:
    case 1:
        $info = $connection -> query("
SELECT concat(U.last_name, ' ', U.first_name) AS `агент`, U.login AS 'логин агента', concat(C.last_name, ' ', C.first_name) AS `клиент`, 
O.debt_sum AS `сумма`, F.full_name AS `валюта`,
O.date AS `дата`
FROM debt_history O
INNER JOIN clients C ON C.client_id = O.client_id 
INNER JOIN users U ON U.user_id = O.user_id
INNER JOIN fiats F ON F.fiat_id = O.fiat_id
WHERE U.branch_id = '$branch_id'
ORDER BY `date` DESC
");
        break;
//    case 1:
//        $info = $connection -> query('
//SELECT concat(C.last_name, " ", C.first_name) AS `клиент`,
//O.debt_sum AS `сумма`, F.full_name AS `валюта`,
//O.date AS `дата`
//FROM debt_history O
//INNER JOIN clients C ON C.client_id = O.client_id
//INNER JOIN users U ON U.user_id = O.user_id
//INNER JOIN fiats F ON F.fiat_id = O.fiat_id
//WHERE O.user_id = '.$_SESSION["id"].'
//ORDER BY `date` DESC
//');
//        break;
    default:
        exit();
        break;
}
$options['type'] = 'Debt';
$options['text'] = 'История погашений долгов';
$options['btn-text'] = 'Погасить';
$options['btn'] = 1;
$options['btn-max'] = 2;

$data['fiats'] = $connection -> query('SELECT * FROM fiats');
$data['clients'] = $connection->query('
SELECT DISTINCT concat(C.last_name, " ", C.first_name) AS `client_name`, byname AS `login`, P.sum AS `debt`, fiat_id,concat(C.client_id, "-", P.fiat_id) AS `id`
FROM clients C
INNER JOIN payments P ON P.client_debt_id = C.client_id 
WHERE P.sum > 0
ORDER BY P.sum DESC');
echo template(display_data($info, $options, $data));

