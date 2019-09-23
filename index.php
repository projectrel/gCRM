<?php
include_once 'funcs.php';
if (!isAuthorized()) header("Location: ./login.php");
include_once './components/templates/template.php';
include_once './db.php';
$table = '';
$branch_id = $_SESSION['branch_id'];
$user_id = $_SESSION['id'];


$ownerSumsRaw = $connection->query('
SELECT concat(U.user_id,"-", F.fiat_id) AS `id`, concat(U.last_name, " ", U.first_name) AS "имя", IFNULL(SUM(RR.sum), 0) AS `прибыль`, IFNULL(SUM(RR.sum), 0) + IFNULL(SUM(IH.sum), 0) - IFNULL(UT.sum, 0) AS `остаток`, F.full_name AS `валюта`
FROM users U
JOIN fiats F
LEFT JOIN (
    SELECT S.sum, S.user_as_owner_id, O.fiat_id FROM shares S INNER JOIN orders O ON O.order_id = S.order_id
    ) RR ON RR.user_as_owner_id = U.user_id AND RR.fiat_id = F.fiat_id
LEFT JOIN (
	SELECT U.user_id, IFNULL(SUM(O.sum), 0) + IFNULL(outcome,0) AS `sum`, IFNULL(O.fiat_id, T.fiat_id) AS `fiat_id`
	FROM users U
	LEFT JOIN outgo O ON O.user_as_owner_id = U.user_id
	LEFT JOIN (
		SELECT SUM(sum)/(SELECT COUNT(DISTINCT user_id) FROM users WHERE branch_id = ' . $branch_id . ' AND is_owner = 1) AS `outcome`, fiat_id
		FROM outgo
		INNER JOIN users U ON U.user_id = outgo.user_id
		WHERE user_as_owner_id IS NULL AND outgo.branch_id IS NULL AND U.branch_id = ' . $branch_id . '
		GROUP BY fiat_id
	) T ON T.fiat_id = O.fiat_id OR O.fiat_id IS NULL
	WHERE U.is_owner = 1 AND U.branch_id = ' . $branch_id . '
	GROUP BY U.user_id, O.fiat_id
) UT ON UT.user_id = U.user_id AND UT.fiat_id = F.fiat_id
LEFT JOIN income_history IH ON IH.owner_id = U.user_id AND IH.fiat = F.fiat_id
WHERE U.is_owner = 1 AND U.branch_id = ' . $branch_id . '
GROUP BY U.user_id, F.fiat_id
');


$branches = $connection->query("
SELECT branch_id AS `id`, branch_name
FROM branch
");
$users = $connection->query('
SELECT *
FROM users
');
$data['branches'] = $branches;
$data['fiats'] = $connection->query("
SELECT * FROM fiats
");
$data['clients'] = $connection->query('
SELECT user_id AS `owner_id`, concat(last_name, " ", first_name) AS `name`
FROM users
WHERE is_owner = 1 AND branch_id = ' . $branch_id . '
');
$data['users'] = $users;
$options['type'] = 'Owner-Stats';
$options['text'] = 'Владельцы';
$options['range'] = 1;
$options['coins'] = 1;
$options['modal'] = 'Outgo-modal';
$table .= display_data($ownerSumsRaw, $options, $data);
$options = [];

switch (accessLevel()) {
    case 2:
    case 1:
        $debtorsData = $connection->query('
SELECT DISTINCT concat(C.last_name, " ", C.first_name) AS "Полное имя", byname AS `Имя`, phone_number AS `телефон`, email AS `почта`, P.sum AS `долг`, C.client_id AS `id`, F.name AS `валюта`, concat(C.client_id, "-", F.fiat_id) AS `id`
FROM clients C
INNER JOIN payments P ON P.client_debt_id = C.client_id 
INNER JOIN fiats F ON P.fiat_id = F.fiat_id 
WHERE C.user_id IN(
    SELECT user_id
    FROM users
    WHERE branch_id = ' . $branch_id . '
) AND P.sum > 0
ORDER BY P.sum DESC');
        $debtorsList = $connection->query('
SELECT DISTINCT concat(C.last_name, " ", C.first_name) AS `client_name`, byname AS `login`, P.sum AS `debt`, fiat_id, concat(C.client_id, "-", P.fiat_id) AS `id`
FROM clients C
INNER JOIN payments P ON P.client_debt_id = C.client_id 
WHERE C.user_id IN(
    SELECT user_id
    FROM users
    WHERE branch_id = ' . $branch_id . '
) AND P.sum > 0
ORDER BY P.sum DESC
');
        $sumDebtsRaw = $connection->query('
SELECT SUM(P.sum) AS `sum`, G.fiat_id, full_name
FROM clients F
INNER JOIN payments P ON P.client_debt_id = F.client_id 
INNER JOIN fiats G ON P.fiat_id = G.fiat_id  
WHERE F.client_id IN(
    SELECT DISTINCT C.client_id
    FROM clients C
    WHERE C.user_id IN(
        SELECT U.user_id
        FROM users U
        WHERE U.branch_id = ' . $branch_id . '
)) AND P.sum > 0
GROUP BY G.fiat_id
');
        $rollbackData = $connection->query('
SELECT DISTINCT concat(C.last_name, " ", C.first_name) AS "Полное имя", byname AS `Имя`, phone_number AS `телефон`, email AS `почта`, P.sum AS `откат`, concat(C.client_id, "-", F.fiat_id) AS `id`, F.name AS `валюта`
FROM clients C
INNER JOIN payments P ON P.client_rollback_id = C.client_id 
INNER JOIN fiats F ON P.fiat_id = F.fiat_id 
WHERE C.user_id IN(
    SELECT user_id
    FROM users
    WHERE branch_id = ' . $branch_id . ') AND P.sum > 0
');
        $rollbackList = $connection->query('
SELECT DISTINCT concat(last_name, " ", first_name) AS `client_name`, concat(C.client_id, "-", P.fiat_id) AS `id`,
byname AS `login`, P.sum AS `rollback_sum`, fiat_id
FROM clients C
INNER JOIN payments P ON P.client_rollback_id = C.client_id 
WHERE C.user_id IN(
    SELECT user_id
    FROM users
    WHERE branch_id = ' . $branch_id . ') AND P.sum > 0
');
        $rollbackSum = $connection->query('
SELECT SUM(P.sum) AS `sum`, F.fiat_id, full_name
FROM clients C
INNER JOIN payments P ON P.client_rollback_id = C.client_id 
INNER JOIN fiats F ON P.fiat_id = F.fiat_id  
WHERE C.rollback_sum > 0 AND C.user_id IN(
        SELECT user_id
        FROM users
        WHERE branch_id = ' . $branch_id . ')
GROUP BY F.fiat_id
');
        break;
    case 3:
        $debtorsData = $connection->query('
SELECT DISTINCT concat(C.last_name, " ", C.first_name) AS "Полное имя", byname AS `Имя`, phone_number AS `телефон`, email AS `почта`, P.sum AS `долг`, client_id AS `id`, F.name AS `валюта`, concat(C.client_id, "-", F.fiat_id) AS `id`
FROM clients C
INNER JOIN payments P ON P.client_debt_id = C.client_id 
INNER JOIN fiats F ON P.fiat_id = F.fiat_id 
WHERE P.sum > 0
ORDER BY P.sum DESC');
        $debtorsList = $connection->query('
SELECT DISTINCT concat(C.last_name, " ", C.first_name) AS `client_name`, byname AS `login`, P.sum AS `debt`, fiat_id,concat(C.client_id, "-", P.fiat_id) AS `id`
FROM clients C
INNER JOIN payments P ON P.client_debt_id = C.client_id 
WHERE P.sum > 0
ORDER BY P.sum DESC');
        $sumDebtsRaw = $connection->query('
SELECT SUM(P.sum) AS `sum`, F.fiat_id, full_name
FROM clients C
INNER JOIN payments P ON P.client_debt_id = C.client_id
INNER JOIN fiats F ON P.fiat_id = F.fiat_id  
GROUP BY F.fiat_id
');
        $rollbackData = $connection->query('
SELECT DISTINCT concat(C.last_name, " ", C.first_name) AS "Полное имя", byname AS `Имя`, phone_number AS `телефон`, email AS `почта`, P.sum AS `откат`, concat(C.client_id, "-", F.fiat_id) AS `id`, F.name AS `валюта`
FROM clients C
INNER JOIN payments P ON P.client_rollback_id = C.client_id 
INNER JOIN fiats F ON P.fiat_id = F.fiat_id 
WHERE P.sum > 0
');
        $rollbackList = $connection->query('
SELECT DISTINCT concat(last_name, " ", first_name) AS `client_name`, concat(C.client_id, "-", P.fiat_id) AS `id`,
byname AS `login`, P.sum AS `rollback_sum`, fiat_id
FROM clients C
INNER JOIN payments P ON P.client_rollback_id = C.client_id 
WHERE P.sum > 0
');
        $rollbackSum = $connection->query('
SELECT SUM(P.sum) AS `sum`, F.fiat_id, full_name
FROM clients C
INNER JOIN payments P ON P.client_rollback_id = C.client_id 
INNER JOIN fiats F ON P.fiat_id = F.fiat_id  
GROUP BY F.fiat_id
');
        break;
//    case 1:
//        $debtorsData = $connection->query('
//SELECT DISTINCT concat(C.last_name, " ", C.first_name) AS "Полное имя", byname AS `Имя`, phone_number AS `телефон`, email AS `почта`, P.sum AS `долг`, C.client_id AS `id`, F.name AS `валюта`, concat(C.client_id, "-", F.fiat_id) AS `id`
//FROM clients C
//INNER JOIN payments P ON P.client_debt_id = C.client_id
//INNER JOIN fiats F ON P.fiat_id = F.fiat_id
//WHERE C.user_id = ' . $_SESSION["id"] . ' AND P.sum > 0
//ORDER BY P.sum DESC');
//        $debtorsList = $connection->query('
//SELECT DISTINCT concat(C.last_name, " ", C.first_name) AS `client_name`, byname AS `login`, P.sum AS `debt`, fiat_id, concat(C.client_id, "-", P.fiat_id) AS `id`
//FROM clients C
//INNER JOIN payments P ON P.client_debt_id = C.client_id
//WHERE C.user_id = ' . $_SESSION["id"] . ' AND P.sum > 0
//ORDER BY P.sum DESC');
//        $sumDebtsRaw = $connection->query('
//SELECT SUM(P.sum) AS `sum`, G.fiat_id, full_name
//FROM clients F
//INNER JOIN payments P ON P.client_debt_id = F.client_id
//INNER JOIN fiats G ON P.fiat_id = G.fiat_id
//WHERE F.client_id IN(
//    SELECT DISTINCT C.client_id
//    FROM clients C
//    WHERE C.user_id = ' . $_SESSION["id"] . '
//    )
//GROUP BY G.fiat_id
//');
//        $rollbackData = $connection->query('
//SELECT DISTINCT concat(C.last_name, " ", C.first_name) AS "Полное имя",
//byname AS `Имя`, phone_number AS `телефон`, email AS `почта`, P.sum AS `откат`, concat(C.client_id, "-", F.fiat_id) AS `id`, F.name AS `валюта`
//FROM clients C
//INNER JOIN payments P ON P.client_rollback_id = C.client_id
//INNER JOIN fiats F ON P.fiat_id = F.fiat_id
//WHERE C.user_id = ' . $_SESSION["id"] . ' AND P.sum > 0
//');
//        $rollbackList = $connection->query('
//SELECT DISTINCT concat(last_name, " ", first_name) AS `client_name`, concat(C.client_id, "-", P.fiat_id) AS `id`,
//byname AS `login`, P.sum AS `rollback_sum`, fiat_id
//FROM clients C
//INNER JOIN payments P ON P.client_rollback_id = C.client_id
//WHERE C.user_id = ' . $_SESSION["id"] . ' AND P.sum > 0
//');
//        $rollbackSum = $connection->query('
//SELECT SUM(P.sum) AS `sum`, F.fiat_id, full_name
//FROM clients C
//INNER JOIN payments P ON P.client_rollback_id = C.client_id
//INNER JOIN fiats F ON P.fiat_id = F.fiat_id
//WHERE C.rollback_sum > 0 AND C.user_id = ' . $_SESSION["id"] . '
//GROUP BY F.fiat_id
//');
//        break;
}
$fiats = $connection->query("SELECT * FROM fiats");
$data['fiats'] = $fiats;
$data['clients'] = $debtorsList;

$options['type'] = 'Debt';
$options['text'] = 'Должники';
if (accessLevel() < 3)
    $options['coins'] = true;
$options['btn-text'] = 'Погасить';
$options['btn'] = 1;
$options['btn-max'] = 2;
$options['modal'] = 'Debt-Modal';
$table .= display_data($debtorsData, $options, $data);

$sumDebts = $sumDebtsRaw ? mysqliToArray($sumDebtsRaw) : null;
if ($sumDebts) {
    $empty = true;
    foreach ($sumDebts as $key => $var) {
        if ($var['sum'] > 0) $empty = false;
    }
    if (!$empty) {
        $table .= '<h3 >Всего: ';
        $i = 0;
        for (; $i < count($sumDebts) - 1; $i++) {
            $var = $sumDebts[$i];
            $table .= $var['sum'] . ' ' . $var['full_name'] . ', ';
        }
        $table .= $sumDebts[$i]['sum'] . ' ' . $sumDebts[$i]['full_name'];
        $table .= '</h3>';
    }
}

$fiats = $connection->query("SELECT * FROM fiats");
$data['clients'] = $rollbackList;
$data['fiats'] = $fiats;
$options['type'] = 'Rollback';
$options['text'] = 'Ожидают откаты';
if (accessLevel() < 3){}
    $options['coins'] = true;
$options['btn'] = 1;
$options['btn-max'] = 2;
$options['btn-text'] = 'Выплатить';

$options['modal'] = 'Rollback-Modal';

$table .= display_data($rollbackData, $options, $data);
$sumDebts = $rollbackSum ? mysqliToArray($rollbackSum) : null;
if ($sumDebts) {
    $empty = true;
    foreach ($sumDebts as $key => $var) {
        if ($var['sum'] > 0) $empty = false;
    }
    if (!$empty) {
        $table .= '<h3 >Всего: ';
        $i = 0;
        for (; $i < count($sumDebts) - 1; $i++) {
            $var = $sumDebts[$i];
            $table .= $var['sum'] . ' ' . $var['full_name'] . ', ';
        }
        $table .= $sumDebts[$i]['sum'] . ' ' . $sumDebts[$i]['full_name'];
        $table .= '</h3>';
    }
}


echo template($table);
