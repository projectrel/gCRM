<?php
include_once("../../funcs.php");
if (isset($_POST['method_name'])) {
    include_once("../../db.php");
    $method_name = clean($_POST['method_name']);

    session_start();
    $user_id = $_SESSION['user_id'];
    $branch_id = $_SESSION['branch_id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));

    $exists = mysqli_fetch_assoc($connection->query("SELECT * FROM methods_of_obtaining WHERE method_name='$method_name' AND branch_id='$branch_id'"));
    if ($exists) {
        error("exists");
        return false;
    }
    if (heCan($user_data['role'], 1)) {
        $res = $connection->
        query("
                INSERT INTO methods_of_obtaining (`method_name`, `branch_id`) VALUES('$method_name', '$branch_id') ");
        if ($res) {
            echo json_encode(array("status" => "success"));
            return false;
        } else {
            return error("failed");
        }
    }
    echo error("denied");
} else {
    return error("empty");
}
