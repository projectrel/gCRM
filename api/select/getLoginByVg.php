<?php
include_once("../../db.php");
include_once("../../funcs.php");
$client_id = clean($_GET['client_id']);
$vg_id = clean($_GET['vg_id']);
$res = mysqli_fetch_assoc($connection->
query("SELECT loginByVg, IFNULL(callmaster, -1) AS 'callmaster', IFNULL(description, -1) AS 'description', method_id,
              fiat_id, rollback_1 AS 'rollback' FROM orders WHERE client_id = '$client_id' AND vg_data_id = '$vg_id' ORDER BY `date` DESC LIMIT 1"));
if($res){
    echo json_encode($res);
    return false;
}
echo json_encode(array("loginByVg"=>null));
return false;
