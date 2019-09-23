<?php
if (isset($_POST['name'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $name = clean($_POST['name']);
    session_start();
    $user_id = $_SESSION['user_id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    $exists = mysqliToArray($connection->query("SELECT * FROM virtualgood WHERE `name` = '$name'"));
    if ($exists) {
        error("exists");
        return false;
    }
    if (heCan($user_data['role'], 3)) {
        $res = $connection->
        query("
                INSERT INTO virtualgood (`name`) VALUES('$name') ");
        if ($res) {
            echo json_encode(array("status" => "success"));
            return false;
        } else {
            return error("failed");
        }
    }
    return error("denied");
} else {
    return error("empty");
}
