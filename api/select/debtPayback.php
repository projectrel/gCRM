<?php

include_once("../../funcs.php");
if (!isset($_GET['debt_id']))
    return error("failed");
include_once("../../db.php");
$debt_id = clean($_GET['debt_id']);
$debt_data = mysqli_fetch_assoc($connection->query("
    SELECT * FROM debt_history WHERE `debt_history_id` = '$debt_id'
    "));

if ($debt_data) {
    echo json_encode($debt_data);
    return false;
} else {
    return error("failed");
}



