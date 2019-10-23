<?php
if (isset($_POST['user_id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $newOwnerId = clean($_POST['user_id']);
    $branch = clean($_POST['branch']);
    if(!isset($_SESSION))
    session_start();
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && (heCan($user_data['role'], 1))) {
        $res = $connection->
        query("UPDATE users SET is_owner = 1 WHERE user_id = '$newOwnerId'");
        if ($res) {
            echo json_encode(array("status"=>"success"));
            return false;
        } else {
            return error("failed");
        }

    } else {
        return error("denied");

    }
}
return error("empty");
