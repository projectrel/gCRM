<?php
if (isset($_POST['login']) && isset($_POST['password']) && isset($_POST['role'])
    && isset($_POST['first_name']) && isset($_POST['last_name'])) {

    include_once("../../db.php");
    include_once("../../funcs.php");
    $money = 0;
    $login = clean($_POST['login']);
    $password = password_hash(clean($_POST['password']), PASSWORD_DEFAULT);
    $role = clean($_POST['role']);
    $first_name = clean($_POST['first_name']);
    $last_name = clean($_POST['last_name']);
    $telegram = clean($_POST['telegram']);

    session_start();
    $user_id = $_SESSION['id'];
    $branch = $_POST['branch'] ? clean($_POST['branch']) : $_SESSION['branch_id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    $check_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE login='$login'"));
    if ($check_data) {
        return error("exists");
    }
    if (heCan($user_data['role'], 2)) {
        $res = $connection->
        query("INSERT INTO `users` (`login`, `telegram`,`pass_hash`,`first_name`,`last_name`,`role`,`branch_id`) VALUES('$login','$telegram', '$password','$first_name','$last_name','$role','$branch') ");
        if ($res) {
            echo json_encode(array("status"=>"success"));
            return false;
        } else {
            error("failed");
            return false;
        }
    }
    return error("denied");
} else {
    return  error("empty");
}
