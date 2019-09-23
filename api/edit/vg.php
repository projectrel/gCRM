<?php
if (isset($_POST['name']) && isset($_POST['in_percent']) && isset($_POST['out_percent'])) {

    include_once("../../db.php");
    include_once("../../funcs.php");
    $name = clean($_POST['name']);
    $url = clean($_POST['url']);
    $in = clean($_POST['in_percent']);
    $out = clean($_POST['out_percent']);
    $vg_id = clean($_POST['vg_id']);
    $key = clean($_POST['key']);
    session_start();
    $user_id = $_SESSION['id'];
    $branch_id = $_SESSION['branch_id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if (heCan($user_data['role'],2)) {
        $res = $connection->
        query("
        UPDATE `vg_data`
        SET
         `name`='$name',
            `out_percent`='$out',
            `in_percent`='$in',
            `api_url_regexp`='$url',
            `access_key`='$key'
        WHERE 
            `vg_data_id`='$vg_id' 
        AND
              `branch_id`='$branch_id'
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
