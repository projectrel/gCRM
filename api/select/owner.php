<?php
include_once("../../funcs.php");
if (!isset($_POST['owner_id']))
    return error("empty");
include_once("../../db.php");


$owner_id = clean($_POST['owner_id']);
$owner_data = mysqli_fetch_assoc($connection->query("
            SELECT branch_name AS 'name', branch_id AS `id`
            FROM owners 
            WHERE owner_id = '$owner_id'
            "));

if (!$owner_data)
    return error("failed");
echo json_encode($owner_data);


