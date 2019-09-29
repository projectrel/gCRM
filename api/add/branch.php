<?php
if (isset($_POST['name'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $name = clean($_POST['name']);
    $money = clean($_POST['money']);
    session_start();
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && iCan(3)) {
        $check = mysqli_fetch_assoc($connection->query("SELECT * FROM branch WHERE branch_name='$name'"));
        if ($check) {
            return error("exists");
        }
        $res = $connection->
        query("INSERT INTO `branch` (branch_name) VALUES('$name')");
        if ($res) {
            echo json_encode(array("status"=>"success"));
            return false;
        } else {
            return error("failed");
        }

    } else {
        error("denied");
        return false;

    }
}
return error("empty");
