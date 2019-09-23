<?php
if (isset($_POST['name'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $name = clean($_POST['name']);
    $branch_id = clean($_POST['branch_id']);
    $ik_id = clean($_POST['ik_id']);
    session_start();
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && (heCan($user_data['role'], 2))) {
        $res = $connection->
        query("UPDATE branch SET branch_name='$name', ik_id='$ik_id'WHERE branch_id='$branch_id'");
        if ($res) {
            echo json_encode(array("status"=>"edit-success"));
            return false;
        } else {
            error("failed");
            return false;
        }

    } else {
        error("denied");
        return false;

    }
}
error("empty");
return false;
