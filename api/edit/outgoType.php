<?php
include_once("../../funcs.php");

if (!isset($_POST['id'], $_POST['name'])) {
    return error("empty");
}
include_once("../../db.php");
$id = clean($_POST['id']);
$newname = clean($_POST['name']);

$result = $connection->query("
    UPDATE outgo_types
    SET outgo_name='$newname'
    WHERE outgo_type_id=$id
");
if (!$result) {
    error("failed");
    return false;
}

echo json_encode(array("status" => "success"));
return false;

