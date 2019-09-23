<?php
if (isset($_POST['id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $id = clean($_POST['id']);
    session_start();
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && (heCan($user_data['role'], 2))) {
        $res = $connection->
        query("UPDATE users SET active= NOT active  WHERE user_id='$id'");
        if ($res) {
            echo json_encode(array("status"=>"edit-success"));
            return false;
        } else {
            return  error("failed");
        }

    } else {
        return  error("denied");

    }
}
return error("empty");