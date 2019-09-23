<?php
include_once '../../funcs.php';
if (!isAuthorized()) header("Location: ./login.php");
include_once '../../db.php';
session_start();
$branch_id = $_SESSION['branch_id'];


// ---------------------TYPE 4-------------------

$options['text'] = 'Оборотная ведомость';
$options['type'] = 'Stat3';
$options['prepared'] = true;

    $querry = "SELECT 'расход' AS `тип`, O.sum, O.date, F.full_name AS `валюта`, IFNULL(concat(UU.first_name, ' ', UU.last_name), '-') AS 'владелец', '-' AS 'клиент'
FROM outgo O
INNER JOIN fiats F ON F.fiat_id = O.fiat_id
LEFT JOIN users UU ON O.user_as_owner_id = UU.user_id   
WHERE O.user_id IN (SELECT user_id FROM users WHERE branch_id=$branch_id)
UNION
SELECT 'выплата отката', R.rollback_sum, R.date, F.full_name, '-' AS 'владелец', concat(CC.first_name, ' ', CC.last_name) AS 'клиент'
FROM rollback_paying R
INNER JOIN fiats F ON F.fiat_id = R.fiat_id
INNER JOIN clients CC ON CC.client_id = R.client_id
WHERE R.user_id IN (SELECT user_id FROM users WHERE branch_id=$branch_id)
UNION
SELECT 'внос денег', I.sum, I.date, F.full_name, concat(O.last_name, ' ', O.first_name) AS 'владелец', '-' AS 'клиент'
FROM income_history I
INNER JOIN fiats F ON F.fiat_id = I.fiat
INNER JOIN users O ON O.user_id = I.owner_id
WHERE I.user_id IN (SELECT user_id FROM users WHERE branch_id=$branch_id)
UNION
SELECT 'продажа', ORD.sum_currency-ORD.order_debt, ORD.date, F.full_name, '-' AS 'владелец',  concat(CCC.last_name, ' ', CCC.first_name) AS 'клиент'
FROM orders ORD
INNER JOIN fiats F ON F.fiat_id = ORD.fiat_id
INNER JOIN clients CCC ON CCC.client_id = ORD.client_id
WHERE ORD.client_id IN (SELECT client_id FROM clients WHERE user_id IN(SELECT user_id FROM users WHERE branch_id=$branch_id))
UNION
SELECT 'погашение долга', D.debt_sum, D.date, F.full_name, '-' AS 'владелец', concat(CCCC.last_name, ' ', CCCC.first_name) AS 'клиент'
FROM debt_history D
INNER JOIN fiats F ON F.fiat_id = D.fiat_id
INNER JOIN clients CCCC ON CCCC.client_id = D.client_id
WHERE D.user_id IN (SELECT user_id FROM users WHERE branch_id=$branch_id)
ORDER BY `date` DESC";

$res = mysqliToArray($connection->query($querry));
if ($res) {
    foreach ($res as $key => $row) {
        $sum[$row['валюта']] = 0;
    }
    for ($i = count($res) - 1; $i >= 0; $i--) {
        $row = $res[$i];
        if ($row['тип'] == 'выплата отката' || $row['тип'] == 'расход') {
            $sum[$row['валюта']] -= $row['sum'];
        } else $sum[$row['валюта']] += $row['sum'];
        foreach ($sum as $fiat => $val) {
            $row['остаток в ' . $fiat] = $val;
        }
        $res[$i] = $row;
    }
}
$result = display_data($res, $options);


echo $result;