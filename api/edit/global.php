<?php
if (isset($_POST['name'])) {

    include_once("../../db.php");
    include_once("../../funcs.php");
    $name = clean($_POST['name']);
    $vg_id = clean($_POST['vg_id']);
    session_start();
    $user_id = $_SESSION['user_id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if (heCan($user_data['role'],3)) {
        $res = $connection->
        query("
        UPDATE `virtualgood`
        SET
            `name` = '$name'
        WHERE 
            `vg_id`='$vg_id' 
        ");
        if ($res) {
            echo json_encode(array("status"=>"edit-success"));
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
