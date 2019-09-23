<?php
if (isset($_POST['login']) && isset($_POST['password'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $login = clean($_POST['login']);
    $password = clean($_POST['password']);
    // $remember_me = clean($_POST['remember_me']);
    $user_data = mysqli_fetch_assoc($connection->query("
        SELECT U.active, last_name, first_name, login, role, B.branch_name, U.branch_id, pass_hash, user_id, is_owner
        FROM users U 
        INNER JOIN branch B ON B.branch_id = U.branch_id 
        WHERE login='$login'
    "));
    if ($login != $user_data['login']) {
        error("login");
        return false;
    }
    if (!password_verify($password, $user_data['pass_hash'])) {
        error("pass");
        return false;
    }
    if (!$user_data['active']) {
        error("inactive");
        return false;
    }
    session_start();
    $_SESSION['name'] = $user_data['first_name'] . ' ' . $user_data['last_name'];
    $_SESSION['login'] = $user_data['login'];
    $_SESSION['role'] = $user_data['role'];
    $_SESSION['password'] = $user_data['pass_hash'];
    $_SESSION['id'] = $user_data['user_id'];
    $_SESSION['branch'] = $user_data['branch_name'];
    $_SESSION['branch_id'] = $user_data['branch_id'];
    $_SESSION['is_owner'] = $user_data['is_owner'];
    //$_SESSION['remember_me'] = $remember_me; in future
    if($_SESSION['role'] == 'agent'){
        echo json_encode(array("status"=>'sales'));
        return false;
    }
    echo json_encode(array("status"=>'success'));
}
