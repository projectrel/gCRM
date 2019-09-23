<?php
if (isset($_POST['name']) && isset($_POST['fiat'])&& isset($_POST['full_name']) && isset($_POST['code'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $fiat_id = clean($_POST['fiat']);
    $name = clean($_POST['name']);
    $full_name = clean($_POST['full_name']);
    $code = clean($_POST['code']);
    session_start();
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && (heCan($user_data['role'], 3))) {
        $res = $connection->
        query("UPDATE fiats SET `name`='$name', full_name='$full_name', code='$code'
                     WHERE fiat_id='$fiat_id'");
        if ($res) {
            echo json_encode(array("status"=>"edit-success"));
            return false;
        } else {
            error("failed");
            return false;
        }

    } else {
        error("denied");
        return false;

    }
}
error("empty");
return false;
