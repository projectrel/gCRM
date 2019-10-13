<?php

include_once("../../../funcs.php");
if (!isset($_GET['method_id'], $_GET['vg_id']))
    error("empty");

include_once("../../../db.php");
$fiat_id = getFiatIdByMethod($connection, $_GET['method_id']);
$vg_id = clean($_GET['vg_id']);
$res = mysqli_fetch_assoc($connection->
query("SELECT IFNULL(`sum`,0) AS `sum` FROM `payments` 
              WHERE vg_data_debt_id = '$vg_id' AND fiat_id = '$fiat_id'
"));
if ($res) {
    echo json_encode($res);

} else {
    echo json_encode(array("sum" => 0));
}
return false;