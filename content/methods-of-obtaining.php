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
$more_data['fiats'] =  $connection->query("
SELECT * FROM fiats
");
switch (accessLevel()) {
    case 3:
        $data = '
SELECT MOO.method_id AS `id`, method_name AS `название`,  branch_name AS `предприятие`, F.full_name AS `валюта`,
 MOO.is_active AS `статус`, method_name AS `название`, participates_in_balance AS `участие в балансе`, IFNULL(MAX(LC.change_date),"-") AS "пос. редакт."
FROM `methods_of_obtaining` MOO INNER JOIN `payments` P ON  MOO.method_id = P.method_id
INNER JOIN `fiats` F ON F.fiat_id = P.fiat_id
INNER JOIN `branch` B ON MOO.branch_id = B.branch_id 
LEFT OUTER JOIN changes LC ON MOO.method_id = LC.method_id
GROUP BY MOO.method_id
';
        break;
    case 1:
        $data = "
SELECT MOO.method_id AS `id`, method_name AS `название`, F.full_name AS `валюта`, is_active AS `статус`, method_name AS `название`, participates_in_balance AS `участие в балансе`
FROM `methods_of_obtaining` MOO INNER JOIN `payments` P ON  MOO.method_id = P.method_id
INNER JOIN `fiats` F ON F.fiat_id = P.fiat_id 

WHERE branch_id = '$branch_id'
";
        break;
    case 2:
        $data = "
SELECT MOO.method_id AS `id`, method_name AS `название`, F.full_name AS `валюта`, is_active AS `статус`, method_name AS `название`, participates_in_balance AS `участие в балансе`,
IFNULL(MAX(LC.change_date),'-') AS 'пос. редакт.'
FROM `methods_of_obtaining` MOO INNER JOIN `payments` P ON  MOO.method_id = P.method_id
INNER JOIN `fiats` F ON F.fiat_id = P.fiat_id  LEFT OUTER JOIN changes LC ON MOO.method_id = LC.method_id

WHERE branch_id = '$branch_id'
GROUP BY MOO.method_id
";
        break;

}


echo template(display_data($connection->query($data), $options,$more_data));

