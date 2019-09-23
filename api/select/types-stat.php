<?php
include_once '../../funcs.php';
if (!isAuthorized()) header("Location: ./login.php");
include_once '../../db.php';
session_start();
$branch_id = $_SESSION['branch_id'];


$start = $_POST['start'];
$end = $_POST['end'];


$querry = "
        SELECT OT.outgo_type_id, F.name AS `fiat_name`, SUM(IFNULL(O.sum, 0)) AS `sum`
        FROM outgo_types OT
        LEFT JOIN outgo O ON O.outgo_type_id = OT.outgo_type_id
        LEFT JOIN fiats F ON F.fiat_id = O.fiat_id
        WHERE ((O.date >= '" . $start . "' AND O.date <= '" . $end . "') OR O.outgo_type_id IS NULL) AND OT.branch_id = '$branch_id' AND OT.outgo_type_id != 1
        GROUP BY OT.outgo_type_id, F.fiat_id
        ORDER BY OT.outgo_type_id, F.fiat_id";
//include "../../dev/ChromePhp.php";
//ChromePhp::log($querry);
$result = mysqliToArray($connection->query($querry));
echo json_encode($result);