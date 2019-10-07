<?php
include_once("../../funcs.php");

if (!isset($_POST['id'], $_POST['name'])) {
    return error("empty");
}
include_once("../../db.php");
$outgo_type_id = clean($_POST['id']);
$newname = clean($_POST['name']);

$result = $connection->query("
    UPDATE outgo_types
    SET outgo_name='$newname'
    WHERE outgo_type_id=$outgo_type_id
");
if (!$result && !save_change_info($connection,'outgo_type',$outgo_type_id)) {
    error("failed");
    return false;
}

echo json_encode(array("status" => "success"));
return false;

