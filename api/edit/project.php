<?php
include_once("../../funcs.php");
if (isset($_POST['name']) && isset($_POST['project_id'])) {
    include_once("../../db.php");
    $name = clean($_POST['name']);
    $project_id = clean($_POST['project_id']);
    session_start();
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && (heCan($user_data['role'], 1))) {
        $res = $connection->
        query("UPDATE projects SET `project_name`='$name'
                     WHERE project_id='$project_id'");
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
