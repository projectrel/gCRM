<?php
if (isset($_POST['id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $id = clean($_POST['id']);
    if(!isset($_SESSION))
    session_start();
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && (heCan($user_data['role'], 1))) {
        $res = $connection->
        query("UPDATE projects SET `active`= NOT active  WHERE project_id='$id'");
        if ($res) {
            echo json_encode(array("status"=>"edit-success"));
            return false;
        } else {
            return error("failed");
        }

    } else {
        return error("denied");

    }
}
return error("empty");