<?php
include_once("../../funcs.php");
if (isset($_POST['id'], $_POST['active'], $_POST['participates_in_balance'])) {
    include_once("../../db.php");
    $method_id = clean($_POST['id']);
    $active = clean($_POST['active']);
    $participates_in_balance = clean($_POST['participates_in_balance']);
    session_start();
    $user_id = $_SESSION['id'];
    $user_data = $connection->query("SELECT * FROM users WHERE user_id='$user_id'");
    if ($user_data && (heCan(mysqliToArray($user_data)['role'], 1))) {
        $res = $connection->
        query("UPDATE methods_of_obtaining SET participates_in_balance= '$participates_in_balance', is_active = '$active' WHERE method_id = $method_id ");
        if ($res) {
            echo json_encode(array("status" => "change-success"));
            return false;
        } else {
            return error("failed");
        }

    } else {
        return error("denied");

    }
}
return error("empty");

