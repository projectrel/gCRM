<?php
include_once("../../db.php");
include_once("../../funcs.php");
session_start();
$branch_id = $_SESSION['branch_id'];
$branch_data['other'] = mysqliToArray($connection->query("
            SELECT branch_name AS 'name', branch_id AS `id`
            FROM branch 
            WHERE branch_id != '$branch_id'
            "));
$current_branch = array('name' => $_SESSION['branch'], 'id' => $_SESSION['branch_id']);
$branch_data['current'] = $current_branch;
if ($branch_data) {
    echo json_encode($branch_data);
    return false;
} else {
    return error("failed");
}

