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
SELECT B.branch_name AS `отдел`, concat(U.last_name, ' ', U.first_name) AS `агент`,  U.login AS 'логин агента', concat(C.last_name, ' ', C.first_name) AS `клиент`, 
concat(O.rollback_sum, ' ', F.name) AS `сумма`, O.date AS `дата`
FROM rollback_paying O
INNER JOIN clients C ON C.client_id = O.client_id 
INNER JOIN users U ON U.user_id = O.user_id
INNER JOIN branch B ON B.branch_id = U.branch_id
INNER JOIN fiats F ON O.fiat_id = F.fiat_id
ORDER BY `date` DESC
");
        $data['clients'] = $connection -> query('
SELECT concat(last_name, " ", first_name) AS `client_name`, 
byname AS `login`, P.sum AS `rollback_sum`, fiat_id, client_id AS "id"
FROM clients C 
INNER JOIN payments P ON C.client_id = P.client_rollback_id
WHERE  P.sum > 0
');
        break;
    case 2:
    case 1:
        $info = $connection -> query("
SELECT concat(U.last_name, ' ', U.first_name) AS `агент`,  U.login AS 'логин агента', concat(C.last_name, ' ', C.first_name) AS `клиент`, 
concat(O.rollback_sum, ' ', F.name) AS `сумма`, O.date AS `дата`
FROM rollback_paying O
INNER JOIN clients C ON C.client_id = O.client_id 
INNER JOIN users U ON U.user_id = O.user_id
INNER JOIN fiats F ON O.fiat_id = F.fiat_id
WHERE U.branch_id = '$branch_id'
ORDER BY `date` DESC
");
    $data['clients'] = $connection -> query('
SELECT concat(last_name, " ", first_name) AS `client_name`, 
byname AS `login`, P.sum AS `rollback_sum`, fiat_id, client_id AS "id"
FROM clients C 
INNER JOIN payments P ON C.client_id = P.client_rollback_id
WHERE  P.sum > 0 AND user_id IN(SELECT user_id FROM users WHERE branch_id="'.$branch_id.'")
');
//        break;
//    case 1:
//        $info = $connection -> query('
//SELECT concat(C.last_name, " ", C.first_name) AS `клиент`,
//concat(O.rollback_sum, " ", F.name) AS `сумма`, O.date AS `дата`
//FROM rollback_paying O
//INNER JOIN clients C ON C.client_id = O.client_id
//INNER JOIN users U ON U.user_id = O.user_id
//INNER JOIN fiats F ON O.fiat_id = F.fiat_id
//WHERE O.user_id = '.$_SESSION["id"].'
//ORDER BY `date` DESC
//');
        break;
    default:
        exit();
        break;
}
$options['type'] = 'Rollback';
$options['text'] = "История выплат рефералов";
$options['btn'] = 1;
$options['btn-max'] = 2;
$options['btn-text'] = 'Выплатить';
$data['fiats'] = $connection -> query('SELECT * FROM fiats');

echo template(display_data($info, $options, $data));
?>

