<?php
include_once("../../db.php");
include_once("../../funcs.php");
session_start();
$branch_id = isset($_POST['branch_id']) ? clean($_POST['branch_id']) : $_SESSION['branch_id'];
$branch_data = $connection->query("
            SELECT branch_name AS 'name', branch_id AS `id`
            FROM branch 
            WHERE branch_id = '$branch_id'
            ");
$branch_data = $branch_data ? mysqli_fetch_assoc($branch_data) : null;

if ($branch_data) {
    echo json_encode($branch_data);
    return false;
} else {
    error("failed");
    return false;
}

