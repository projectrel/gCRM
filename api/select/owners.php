<?php
include_once("../../db.php");
include_once("../../funcs.php");
session_start();
$branch = isset($_POST['branch']) ? clean($_POST['branch']) : $_SESSION['branch_id'];
$owners = mysqliToArray($connection->query("
    SELECT concat(first_name, ' ', last_name) AS `full_name`, user_id AS `id` FROM users WHERE branch_id = '$branch' AND is_owner = 1
    "));

if ($owners) {
    echo json_encode($owners);
    return false;
} else {
    error("failed");
    return false;
}

