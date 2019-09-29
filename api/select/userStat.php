<?php
include_once '../../funcs.php';
if (!isAuthorized()) header("Location: ./login.php");
include_once '../../db.php';
session_start();
$branch_id = $_SESSION['branch_id'];



$start = $_POST['start'];
$end = $_POST['end'];
$type = $_POST['type'];
SWITCH($type){

    case 2:
        $select = "SELECT V.vg_id AS `id`, V.name AS `название`";
        $join = "";
        $join .= "\nLEFT JOIN (
    SELECT O.vg_id, SUM(O.sum_vg) AS `sum`
    FROM orders O
    WHERE (O.date >= '".$start."' AND O.date <= '".$end."')".(!iCan(3) ? " AND O.client_id IN (SELECT client_id FROM clients WHERE user_id IN(SELECT user_id FROM users WHERE branch_id=$branch_id))" : "")."
    GROUP BY O.vg_id
) MMM ON MMM.vg_id = V.vg_id";
        $select .= ", IFNULL(MMM.sum, 0) AS `sum`";

        $querry = "
".$select."
FROM virtualgood V
".$join."
ORDER BY sum";
        break;
    case 3:
        $select = "SELECT concat(V.vg_id, '-', F.fiat_id) AS `id`, V.name AS `название`, F.full_name AS `валюта`";
        $join = "";
        $join .= "\nLEFT JOIN (
    SELECT O.vg_id, SUM(O.sum_currency) AS `sum`, O.fiat_id
    FROM orders O
    WHERE (O.date >= '".$start."' AND O.date <= '".$end."')".(!iCan(3) ? " AND O.client_id IN (SELECT client_id FROM clients WHERE user_id IN(SELECT user_id FROM users WHERE branch_id=$branch_id))" : "")."
    GROUP BY O.vg_id, O.fiat_id
) MMM ON MMM.vg_id = V.vg_id AND MMM.fiat_id = F.fiat_id";
        $select .= ", IFNULL(MMM.sum, 0) AS `sum`";

        $querry = "
".$select."
FROM virtualgood V
JOIN fiats F
".$join."
GROUP BY V.vg_id, F.fiat_id
ORDER BY sum";
        break;
    default:
        $select = "SELECT concat(UU.user_id, '-', FF.fiat_id) AS `id`, concat(UU.first_name, ' ', UU.last_name) AS `владелец`, FF.full_name AS `валюта`";
        $join = "";
        $join .= "LEFT JOIN (
    SELECT O.fiat_id, S.user_as_owner_id, SUM(S.sum) AS `sum` 
    FROM shares S
    INNER JOIN orders O ON O.order_id = S.order_id
    WHERE (O.date >= '" . $start . "' AND O.date <= '" . $end . "')
    GROUP BY O.fiat_id, S.user_as_owner_id
) MMM ON MMM.user_as_owner_id = UU.user_id AND MMM.fiat_id = FF.fiat_id";
        $select .= ", MMM.sum AS `sum`";

        $querry = "
" . $select . "
FROM users UU
JOIN fiats FF
" . $join . "
WHERE UU.is_owner = 1 ".(!iCan(3) ? " AND UU.branch_id = $branch_id" : "")."
";
}
$result = mysqliToArray($connection->query($querry));
echo json_encode($result);