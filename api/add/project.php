<?php
include_once("../../funcs.php");
if (isset($_POST['name'])) {
    include_once("../../db.php");
    $name = clean($_POST['name']);
    session_start();
    $user_id = $_SESSION['id'];
    $branch_id = $_SESSION['branch_id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && iCan(1)) {
        $res = $connection->
        query("INSERT INTO `projects` (`project_name`, `branch_id`) VALUES('$name', '$branch_id')");
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
