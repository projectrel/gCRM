<?php
include_once("../../funcs.php");
if (isset($_POST['method_name'], $_POST['method_id'])) {
    include_once("../../db.php");
    $method_name = clean($_POST['method_name']);
    $method_id = clean($_POST['method_id']);
    session_start();
    $user_id = $_SESSION['user_id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if (heCan($user_data['role'], 1)) {
        $res = $connection->
        query("
        UPDATE `methods_of_obtaining`
        SET
            `method_name` = '$method_name'
        WHERE 
            `method_id`='$method_id' 
        ");
        if ($res) {
            echo json_encode(array("status" => "edit-success"));
            return false;
        } else {
            error("failed");
            return false;
        }
    }
    error("denied");
    return false;
} else {
    error("empty");
}
