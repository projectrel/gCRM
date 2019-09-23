<?php
if (isset($_POST['branch_id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $branch_id = clean($_POST['branch_id']);
    session_start();
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && (heCan($user_data['role'], 3))) {
        $branch_name = mysqli_fetch_assoc($connection->
        query("SELECT branch_name AS 'name' FROM branch WHERE branch_id= '$branch_id'"))['name'];
        $res = $connection->
        query("UPDATE users SET branch_id= $branch_id WHERE user_id= $user_id ");
        if ($res) {
            $_SESSION['branch'] = $branch_name;
            $_SESSION['branch_id'] = $branch_id;
            echo json_encode(array("status" => "change-success"));
            return false;
        } else {
            return error("failed");
        }

    } else {
        return error("denied");

    }
}
return error("empty");

