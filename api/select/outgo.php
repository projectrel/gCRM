<?php
include_once("../../funcs.php");
if (!isset($_GET['outgo_id']))
    return error("empty");
include_once("../../db.php");


$outgo_id = clean($_GET['outgo_id']);
$outgo_data = mysqli_fetch_assoc($connection->
query("
            SELECT `outgo_id`, `sum`, CASE WHEN `branch_id` IS NOT NULL AND `user_as_owner_id` IS NULL THEN 'branch' ELSE '' END AS 'user_as_owner_id', `outgo_type_id`, `project_id`,`description`,`method_id`
            FROM outgo 
            WHERE outgo_id = '$outgo_id'
            "));

if (!$outgo_data)
    return error("failed");
echo json_encode($outgo_data);


