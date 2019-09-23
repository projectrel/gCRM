<?php
include_once("../../funcs.php");

if (!isset($_POST['name'], $_POST['parentId'])) {
    return error("empty");
}
include_once("../../db.php");
$name = clean($_POST['name']);
$parentId = clean($_POST['parentId']);

session_start();
$branch_id = $_SESSION['branch_id'];

$siblings = mysqli_fetch_assoc($connection->query("
    SELECT outgo_type_id FROM outgo_types
    WHERE outgo_type_id IN (
        SELECT son_id FROM outgo_types_relative
        WHERE parent_id = '$parentId'
    )
    ORDER BY outgo_type_id DESC
"));

if($siblings){
    $nextId = intval($siblings['outgo_type_id']) + 1;
}else{
    $nextId = $parentId."00";
}

$result = $connection->query("
    INSERT INTO outgo_types
    (outgo_type_id, outgo_name, branch_id)
    VALUES('$nextId','$name', '$branch_id');
");
if (!$result) {
    error("failed");
    return false;
}

$result = $connection->query("
    INSERT INTO outgo_types_relative
    (parent_id, son_id)
    VALUES('$parentId', '$nextId');
");
if (!$result) {
    error("failed");
    return false;
}

echo json_encode(array("status" => "success"));
return false;

