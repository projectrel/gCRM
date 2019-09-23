<?php
if (isset($_POST['client_id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $client_id = clean($_POST['client_id']);
    session_start();
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("
        SELECT * 
        FROM users 
        WHERE user_id='$user_id'"));
    if (!$user_data) return false;
    $client_data = mysqli_fetch_assoc($connection->query("
        SELECT * 
        FROM clients 
        WHERE client_id='$client_id'"));

    if (is_numeric($client_data['callmaster'])) {
        echo '';
    } else {
        return false;
    }
}

