<?php
include_once("../../funcs.php");
if (isset($_POST['sum'], $_POST['method_id'])) {
    include_once("../../db.php");
    $sum = clean($_POST['sum']);
    $owner_id = clean($_POST['owner_id']);
    $method_id = clean($_POST['method_id']);
    $project_id = clean($_POST['project_id']);
    $type = clean($_POST['type']);
    $description = clean($_POST['description']);
    session_start();
    $branch_id = $_SESSION['branch_id'];
    $user_id = $_SESSION['id'];

    $VALUES = "'$user_id','$sum', '$description', '$method_id'";
    $PARAMS = "`user_id`,`sum`, `description`, `method_id`";

    if ($type) {
        $VALUES .= ", '$type'";
        $PARAMS .= ", `outgo_type_id`";
    }
    if ($owner_id == "branch") {
        $VALUES .= ", '$branch_id'";
        $PARAMS .= ", `branch_id`";
    } elseif ($owner_id) {
        $VALUES .= ", '$owner_id'";
        $PARAMS .= ", `user_as_owner_id`";
    }
    if($project_id){
        $VALUES .= ", '$project_id'";
        $PARAMS .= ", `project_id`";
    }
    $res = $connection->query("INSERT INTO outgo ($PARAMS) VALUES($VALUES);");

if ($res &&  updatemethodMoney($connection, $method_id, -$sum)) {
    echo json_encode(array("status" => "success"));
    return false;
} else {
    error("failed");
    return false;
}
} else {
    return error("empty");
}
