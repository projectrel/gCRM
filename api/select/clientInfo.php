<?php
include_once("../../db.php");
include_once("../../funcs.php");
$client_id = clean($_POST['client_id']);
$client_data['rollback'] = mysqliToArray($connection->query("
   SELECT client_id AS `id`, IFNULL(PP.sum, 0) AS `rollback`, C.first_name, C.last_name, IFNULL(F.name, 'грн') AS `fiat`
        FROM clients C
        LEFT JOIN payments PP ON PP.client_rollback_id = C.client_id
        LEFT JOIN fiats F ON F.fiat_id = PP.fiat_id
        WHERE C.client_id = '$client_id'
    "));
$client_data['debt'] = mysqliToArray($connection->query("
   SELECT client_id AS `id`, IFNULL(PP.sum, 0) AS `debt`, C.first_name, C.last_name, IFNULL(F.name, 'грн') AS `fiat`
        FROM clients C
        LEFT JOIN payments PP ON PP.client_debt_id = C.client_id
        LEFT JOIN fiats F ON F.fiat_id = PP.fiat_id
        WHERE C.client_id = '$client_id'
    "));
if ($client_data) {
    echo json_encode($client_data);
    return false;
} else {
    error("failed");
    return false;
}
