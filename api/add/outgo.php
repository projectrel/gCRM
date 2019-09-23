<?php
include_once("../../funcs.php");
if (isset($_POST['sum'], $_POST['fiat'])) {
    include_once("../../db.php");
    $sum = clean($_POST['sum']);
    $owner = clean($_POST['owner']);
    $fiat = clean($_POST['fiat']);
    $project = clean($_POST['project']);
    $type = clean($_POST['type']);
    $descr = clean($_POST['description']);
    session_start();
    $branch_id = $_SESSION['branch_id'];
    date_default_timezone_set('Europe/Kiev');
    $date = date('Y-m-d H:i:s');
    $user_id = $_SESSION['id'];

    $VALUES = "'$user_id','$sum', '$date', '$descr', '$fiat'";
    $PARAMS = "`user_id`,`sum`, `date`, `description`, `fiat_id`";

    if ($type) {
        $VALUES .= ", '$type'";
        $PARAMS .= ", `outgo_type_id`";
    }
    if ($owner == "branch") {
        $VALUES .= ", '$branch_id'";
        $PARAMS .= ", `branch_id`";
    } elseif ($owner) {
        $VALUES .= ", '$owner'";
        $PARAMS .= ", `user_as_owner_id`";
    }
    if($project){
        $VALUES .= ", '$project'";
        $PARAMS .= ", `project_id`";
    }
    $res = $connection->query("INSERT INTO outgo ($PARAMS) VALUES($VALUES);");
if ($res)
    updateBranchMoney($connection, $branch_id, -$sum, $fiat);
if ($res) {
    echo json_encode(array("status" => "success"));
    return false;
} else {
    error("failed");
    return false;
}
} else {
    return error("empty");
}
