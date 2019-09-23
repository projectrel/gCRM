<?php
include_once("../../funcs.php");

if (!isset($_POST['id'])) {
    return error("empty");
}
include_once("../../db.php");
$id = clean($_POST['id']);

$status_was = (int)mysqli_fetch_assoc($connection->query("
    SELECT * FROM outgo_types
    WHERE outgo_type_id='$id'
    "))['active'];
$status_to_do = $status_was === 0 ? 1 : 0;

$parent_activity = (int)mysqli_fetch_assoc($connection->query("
    SELECT * FROM outgo_types
    WHERE outgo_type_id IN (
        SELECT parent_id FROM outgo_types_relative
        WHERE son_id = '$id'
    )
"))['active'];

if($parent_activity == 0){
   error("denied");
    return false;
}


$types = getOutGoTypes($connection);

$type = getTypeByTypes($types, $id);

$children = children_list($type, $types);
$children_names = "";

foreach ($children as $child) {
    $children_names .= "'".$child['outgo_type_id']."',";
}
$children_names = substr($children_names, 0, -1);


$query = "UPDATE outgo_types SET `active`='$status_to_do' WHERE outgo_type_id IN (".$children_names.")";
$update_hall = $connection->query($query);

echo json_encode(array("status"=>"success", "nodes"=>$children_names, "status_to_do"=>$status_to_do));
return false;

