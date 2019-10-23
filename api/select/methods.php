<?php
include_once("../../db.php");
include_once("../../funcs.php");
if(!isset($_SESSION))
    session_start();
$branch_id = $_SESSION['branch_id'];
$query_get_methods = "
SELECT MOO.method_id, concat(MOO.method_name,'(',F.full_name,')') AS `full_name` FROM `methods_of_obtaining` MOO 
INNER JOIN payments P ON MOO.method_id = P.method_id
INNER JOIN fiats F ON P.fiat_id = F.fiat_id";
$methods = mysqliToArray($connection->query($query_get_methods));
if ($methods) {
    echo json_encode($methods);
    return false;
} else {
    return error("failed");
}