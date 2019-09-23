<?php
include_once('../../funcs.php');
if (!isAuthorized()) header("Location: ./login.php");
include_once '../../db.php';
$branch_id = $_SESSION['branch_id'];


$ownerSumsRaw = $connection->query("
SELECT F.full_name, IFNULL(SUM(P.sum), 0) AS `sum`
FROM fiats F
LEFT JOIN payments P ON P.branch_id = '$branch_id' AND F.fiat_id = P.fiat_id
GROUP BY F.full_name
");

echo json_encode(mysqliToArray($ownerSumsRaw));