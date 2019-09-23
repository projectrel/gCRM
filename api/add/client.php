<?php
if (isset($_POST['first_name'])) {

    include_once("../../db.php");
    include_once("../../funcs.php");
    $debt = 0.0;
    $rollback_sum = 0.0;
    $first_name = clean($_POST['first_name']);
    $last_name = isset($_POST['last_name']) ? clean($_POST['last_name']) : null;
    $byname = isset($_POST['byname']) ? clean($_POST['byname']) : null;
    $pass = isset($_POST['password']) ? clean($_POST['password']) : null;
    $max_debt = isset($_POST['max_debt']) ? (int)clean($_POST['max_debt']) : 0;

    $description = clean($_POST['description']);
    $telegram = clean($_POST['telegram']);
    $phone = clean($_POST['phone']);
    $pay_page = $_POST['pay_page'] === "true" ? 1 : 0;
    $payment_system = $_POST['payment_system'] === "true" ? 1 : 0;
    $pay_in_debt = $_POST['pay_in_debt'] === "true" ? 1 : 0;
    $email = isset($_POST['email']) ? clean($_POST['email']) : " ";
    session_start();
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    $client = true;
    $check_client = mysqli_fetch_assoc($connection->query("SELECT client_id AS `id` FROM clients WHERE byname='$byname'"));
    if ($check_client && $byname) {
        return error("exists");
    }
    if ($user_data && heCan($user_data['role'], 1)) {
        $res = $connection->
        query("
        INSERT INTO clients (`user_id`, `last_name`, `first_name`, `byname`, `phone_number`, `email`, `description`, `telegram`, `login`, `password`, `pay_in_debt`, `payment_system`, `pay_page`, `max_debt`) 
        VALUES('$user_id', '$last_name','$first_name','$byname','$phone','$email','$description', '$telegram', '$byname', '$pass', '$pay_in_debt', '$payment_system', '$pay_page', '$max_debt') ");
        $lastid = mysqli_fetch_assoc($connection ->query('SELECT client_id AS `id` FROM clients ORDER BY client_id DESC LIMIT 1'))['id'];
        if ($res) {
            echo json_encode(array("status"=>"success", "id" => $lastid));
            return false;
        } else {
            return error("failed");
        }

    } else {
        return error("denied");
    }
}else{
    return error("empty");
}
