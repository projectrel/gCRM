<?php
include_once('../../funcs.php');
if (!isAuthorized()) header("Location: ./login.php");
include_once '../../db.php';
$branch_id = $_SESSION['branch_id'];
$start = clean($_POST['start']);
$end = clean($_POST['end']);
$query = "
SELECT concat(U.user_id, '-', F.fiat_id) AS `id`, IFNULL(SUM(S.sum), 0) AS `sum`, IFNULL(S.fiat_id, F.fiat_id) AS `fiat_id`
FROM users U
JOIN fiats F
LEFT JOIN (
    SELECT O.fiat_id, S.user_as_owner_id, S.sum 
    FROM shares S
    INNER JOIN orders O ON O.order_id = S.order_id
    WHERE (O.date >= '".$start." 00:00:00' AND O.date <= '".$end." 23:59:59')
) S ON S.user_as_owner_id = U.user_id AND S.fiat_id = F.fiat_id
WHERE U.is_owner = 1 AND U.branch_id = $branch_id
GROUP BY U.user_id, IFNULL(S.fiat_id, F.fiat_id)
";

$ownerSumsRaw = $connection->query($query);

echo $ownerSumsRaw ? json_encode(mysqliToArray($ownerSumsRaw)) : false;