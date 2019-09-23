<?php
session_start();
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $branch_id = $_SESSION['branch_id'];
    include_once("../../db.php");
    include_once("../../funcs.php");
    $data = mysqli_fetch_assoc($connection->query("SELECT * FROM users  WHERE user_id='$user_id'"));
    if (!$data['active'] || $_SESSION['login'] != $data['login']) {
        session_destroy();
        $res['active'] = 'inactive';
        echo json_encode($res);
        exit;
    } else {
        $res['active'] = 'active';
        echo json_encode($res);
        return false;
    }
}