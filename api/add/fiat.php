<?php
if (isset($_POST['name']) && isset($_POST['full_name']) && isset($_POST['code'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $name = clean($_POST['name']);
    $full_name = clean($_POST['full_name']);
    $code = clean($_POST['code']);
    session_start();
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && iCan(3)) {
        $res = $connection->
        query("INSERT INTO `fiats` (`name`, code, full_name) VALUES('$name', '$code', '$full_name')");
        if ($res) {
            echo json_encode(array("status" => "success"));
            return false;
        } else {
            return error("failed");
        }

    } else {
        return error("denied");

    }
}
return error("empty");
