<?php
if (isset($_POST['login']) && isset($_POST['role'])
    && isset($_POST['first_name']) && isset($_POST['last_name'])) {

    include_once("../../db.php");
    include_once("../../funcs.php");
    $login = clean($_POST['login']);
    if($_POST['password'])
        $password =  password_hash($_POST['password'], PASSWORD_DEFAULT);

    $role = clean($_POST['role']);
    $first_name = clean($_POST['first_name']);
    $last_name = clean($_POST['last_name']);
    $telegram = clean($_POST['telegram']);
    $edit_user_id = clean($_POST['user_id']);
    session_start();
    $branch = $_POST['branch'] ? clean($_POST['branch']) : $_SESSION['branch_id'];
    $user_id = $_SESSION['id'];
    if ($user_id == $edit_user_id) {
        $_SESSION['name'] = $first_name . ' ' . $last_name;
        if ($_SESSION['branch_id'] !== $branch) {
            $_SESSION['branch'] = mysqli_fetch_assoc($connection->query("SELECT branch_name FROM branch WHERE branch_id='$branch'"))['branch_name'];
        }
        $_SESSION['branch_id'] = $branch;
        $_SESSION['role'] = $role;
        $_SESSION['login'] = $login;
    }
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if (iCan(2)) {
            $res = $connection->
            query("
        UPDATE `users` 
        SET `login`='$login',"
                . ($password ? "`pass_hash` = '$password'," : "") . "
            `first_name` = '$first_name',
            `last_name` = '$last_name',
            `telegram` = '$telegram',
            `role` = '$role',
            `branch_id` = '$branch'
        WHERE `user_id` = '$edit_user_id'");



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
} else {
    error("empty");
}
