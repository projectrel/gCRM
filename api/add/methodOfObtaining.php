<?php
include_once("../../funcs.php");
if (isset($_POST['method_name'], $_POST['fiat_id'])) {
    include_once("../../db.php");
    $method_name = clean($_POST['method_name']);
    $fiat_id = clean($_POST['fiat_id']);

    if(!isset($_SESSION))
    session_start();
    $user_id = $_SESSION['id'];
    $branch_id = $_SESSION['branch_id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));

    $exists = mysqli_fetch_assoc($connection->query("SELECT * FROM methods_of_obtaining WHERE method_name='$method_name' AND branch_id='$branch_id'"));
    if ($exists) {
        error("exists");
        return false;
    }
    if (heCan($user_data['role'], 1)) {
        $res1 = $connection->
        query("
                INSERT INTO methods_of_obtaining (`method_name`, `branch_id`) VALUES('$method_name', '$branch_id') ");
        $method_id = mysqli_fetch_assoc($connection->query("SELECT * FROM methods_of_obtaining WHERE method_name='$method_name' AND branch_id='$branch_id'"))['method_id'];
        $res2 = $connection->query("INSERT INTO `payments`(`fiat_id`, `method_id`) VALUES ('$fiat_id','$method_id')");
        if ($res1 && $res2) {
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
