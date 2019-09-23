<?php
include_once '../../funcs.php';
if (!isAuthorized()) header("Location: ./login.php");
include_once '../../db.php';
session_start();
$branch_id = $_SESSION['branch_id'];

$select = "SELECT concat(UU.user_id, '-', FF.fiat_id) AS `id`, concat(UU.first_name, ' ', UU.last_name) AS `владелец`, FF.full_name AS `валюта`";
$join = "";
$letters = array("A", "B", "C", "D", "E","F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q");
$month_names = array("январь","февраль","март","апрель","май","июнь","июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь");
$week_names = array("вручную", "тек. неделя","прошл. неделя","неделя -2");
$year_names = array("тек. год","прошлый год","все время");

$months = $_POST['months'];
$weeks = $_POST['weeks'];
$years = $_POST['years'];
$index = 0;
$offset = 0;
foreach ($weeks as $week) {
    $L = $letters[$index++];
    $start = $week[0];
    $end = $week[1];
    $join .= "\nLEFT JOIN (
    SELECT O.fiat_id, S.user_as_owner_id, SUM(S.sum) AS `sum` 
    FROM shares S
    INNER JOIN orders O ON O.order_id = S.order_id
    WHERE (O.date >= '".$start."' AND O.date <= '".$end."')
    GROUP BY O.fiat_id, S.user_as_owner_id
) ".$L." ON ".$L.".user_as_owner_id = UU.user_id AND ".$L.".fiat_id = FF.fiat_id";
    $select .= ", IFNULL(".$L.".sum, 0) AS `".$week_names[$index-1-$offset]."`";
}

$offset = $index;

foreach ($months as $month) {
    $L = $letters[$index++];
    $start = $month[0];
    $end = $month[1];
    $join .= "\nLEFT JOIN (
    SELECT O.fiat_id, S.user_as_owner_id, SUM(S.sum) AS `sum`
    FROM shares S
    INNER JOIN orders O ON O.order_id = S.order_id
    WHERE (O.date >= '".$start."' AND O.date <= '".$end."')
    GROUP BY O.fiat_id, S.user_as_owner_id
) ".$L." ON ".$L.".user_as_owner_id = UU.user_id AND ".$L.".fiat_id = FF.fiat_id";
    $select .= ", IFNULL(".$L.".sum, 0) AS `".$month_names[$month[2]]." ".$month[3]."`";
}

$offset = $index;

foreach ($years as $year) {
    $L = $letters[$index++];
    $start = $year[0];
    $end = $year[1];
    $join .= "\nLEFT JOIN (
    SELECT O.fiat_id, S.user_as_owner_id, SUM(S.sum) AS `sum`
    FROM shares S
    INNER JOIN orders O ON O.order_id = S.order_id
    WHERE (O.date >= '".$start."' AND O.date <= '".$end."')
    GROUP BY O.fiat_id, S.user_as_owner_id
) ".$L." ON ".$L.".user_as_owner_id = UU.user_id AND ".$L.".fiat_id = FF.fiat_id";
    $select .= ", IFNULL(".$L.".sum, 0) AS `".$year[2]."`";
}
$querry = "
".$select."
FROM users UU
JOIN fiats FF
".$join."
WHERE UU.is_owner = 1";

if(!iCan(3)){
    $querry .= " AND UU.branch_id = $branch_id";
}

$options['text'] = 'Прибыль';
$options['type'] = 'Stat1';
$options['range'] = 1;

$result = display_data($connection->query($querry), $options);
// ---------------------TYPE 2-------------------
$select = "SELECT V.vg_id AS `id`, V.name AS `название`";
$join = "";
$index = 0;
$offset = $index;

foreach ($weeks as $week) {
    $L = $letters[$index++];
    $start = $week[0];
    $end = $week[1];
    $join .= "\nLEFT JOIN (
    SELECT O.vg_id, SUM(O.sum_vg) AS `sum`
    FROM orders O
    WHERE (O.date >= '".$start."' AND O.date <= '".$end."')".(!iCan(3) ? " AND O.client_id IN (SELECT client_id FROM clients WHERE user_id IN(SELECT user_id FROM users WHERE branch_id='$branch_id'))" : "")."
    GROUP BY O.vg_id
) ".$L." ON ".$L.".vg_id = V.vg_id";
    $select .= ", IFNULL(".$L.".sum, 0) AS `".$week_names[$index-1-$offset]."`";
}
$offset = $index;

foreach ($months as $month) {
    $L = $letters[$index++];
    $start = $month[0];
    $end = $month[1];
    $join .= "\nLEFT JOIN (
    SELECT O.vg_id, SUM(O.sum_vg) AS `sum`
    FROM orders O
    WHERE (O.date >= '".$start."' AND O.date <= '".$end."')".(!iCan(3) ? " AND O.client_id IN (SELECT client_id FROM clients WHERE user_id IN(SELECT user_id FROM users WHERE branch_id='$branch_id'))" : "")."
    GROUP BY O.vg_id
) ".$L." ON ".$L.".vg_id = V.vg_id";
    $select .= ", IFNULL(".$L.".sum, 0) AS `".$month_names[$month[2]]." ".$month[3]."`";
}
$offset = $index;

foreach ($years as $year) {
    $L = $letters[$index++];
    $start = $year[0];
    $end = $year[1];
    $join .= "\nLEFT JOIN (
    SELECT O.vg_id, SUM(O.sum_vg) AS `sum`
    FROM orders O
    WHERE (O.date >= '".$start."' AND O.date <= '".$end."')".(!iCan(3) ? " AND O.client_id IN (SELECT client_id FROM clients WHERE user_id IN(SELECT user_id FROM users WHERE branch_id='$branch_id'))" : "")."
    GROUP BY O.vg_id
) ".$L." ON ".$L.".vg_id = V.vg_id";
    $select .= ", IFNULL(".$L.".sum, 0) AS `".$year[2]."`";
}
$options['text'] = 'Оборот по VG';
$options['type'] = 'Stat2';
$options['range'] = 2;
$options['switch-vg-stat'] = 1;
$querry = "
".$select."
FROM virtualgood V
".$join."
ORDER BY V.name";

$result .= display_data($connection->query($querry), $options);

// ---------------------TYPE 3-------------------
$select = "SELECT concat(V.vg_id, '-', FF.fiat_id) AS `id`, V.name AS `название`, FF.full_name AS `валюта`";
$join = "";
$index = 0;
$offset = $index;

foreach ($weeks as $week) {
    $L = $letters[$index++];
    $start = $week[0];
    $end = $week[1];
    $join .= "\nLEFT JOIN (
    SELECT O.vg_id, SUM(O.sum_currency) AS `sum`, O.fiat_id
    FROM orders O
    WHERE (O.date >= '".$start."' AND O.date <= '".$end."')".(!iCan(3) ? " AND O.client_id IN (SELECT client_id FROM clients WHERE user_id IN(SELECT user_id FROM users WHERE branch_id='$branch_id'))" : "")."
    GROUP BY O.vg_id, O.fiat_id
) ".$L." ON ".$L.".vg_id = V.vg_id AND ".$L.".fiat_id = FF.fiat_id";
    $select .= ", IFNULL(".$L.".sum, 0) AS `".$week_names[$index-1-$offset]."`";
}
$offset = $index;

foreach ($months as $month) {
    $L = $letters[$index++];
    $start = $month[0];
    $end = $month[1];
    $join .= "\nLEFT JOIN (
    SELECT O.vg_id, SUM(O.sum_currency) AS `sum`, O.fiat_id
    FROM orders O
    WHERE (O.date >= '".$start."' AND O.date <= '".$end."')".(!iCan(3) ? " AND O.client_id IN (SELECT client_id FROM clients WHERE user_id IN(SELECT user_id FROM users WHERE branch_id='$branch_id'))" : "")."
    GROUP BY O.vg_id, O.fiat_id
) ".$L." ON ".$L.".vg_id = V.vg_id AND ".$L.".fiat_id = FF.fiat_id";
    $select .= ", IFNULL(".$L.".sum, 0) AS `".$month_names[$month[2]]." ".$month[3]."`";
}
$offset = $index;

foreach ($years as $year) {
    $L = $letters[$index++];
    $start = $year[0];
    $end = $year[1];
    $join .= "\nLEFT JOIN (
    SELECT O.vg_id, SUM(O.sum_currency) AS `sum`, O.fiat_id
    FROM orders O
    WHERE (O.date >= '".$start."' AND O.date <= '".$end."')".(!iCan(3) ? " AND O.client_id IN (SELECT client_id FROM clients WHERE user_id IN(SELECT user_id FROM users WHERE branch_id='$branch_id'))" : "")."
    GROUP BY O.vg_id, O.fiat_id
) ".$L." ON ".$L.".vg_id = V.vg_id AND ".$L.".fiat_id = FF.fiat_id";
    $select .= ", IFNULL(".$L.".sum, 0) AS `".$year[2]."`";
}
$options['text'] = 'Оборот по VG';
$options['type'] = 'Stat3';
$options['range'] = 3;
$options['switch-vg-stat'] = 1;

$querry = "
".$select."
FROM virtualgood V
JOIN fiats FF
".$join."
GROUP BY V.vg_id, FF.fiat_id
ORDER BY V.name";


$result .= display_data($connection->query($querry), $options);




echo $result;