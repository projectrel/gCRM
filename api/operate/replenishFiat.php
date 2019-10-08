<?php
include_once("../../funcs.php");
if (isset($_POST['method_id']) && isset($_POST['sum']) && isset($_POST['owner'])) {
    include_once("../../db.php");
    session_start();
    $user_id = $_SESSION['id'];
    $branch_id = $_SESSION['branch_id'];
    $method_id = clean($_POST['method_id']);
    $sum = clean($_POST['sum']);
    $owner = $_POST['owner'] != 0 ? $_POST['owner']: $user_id;
    $connection->query("INSERT INTO income_history (`method_id`, `owner_id`, `sum`, `user_id`) VALUES($method_id, $owner, $sum, $user_id)");
    updateMethodMoney($connection, $method_id, $sum);
    echo json_encode(array("status" => "success-replenish"));
    return false;
}
return error("empty");