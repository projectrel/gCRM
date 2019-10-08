<?php
include_once("../../funcs.php");
if (isset($_POST['sum'], $_POST['client_id'], $_POST['method_id'])) {
    include_once("../../db.php");


    $method_id = clean($_POST['method_id']);
    $sum = clean($_POST['sum']);
    $client_id = explode('-', $_POST['client_id'])[0];

    session_start();
    $branch_id = $_SESSION['branch_id'];
    $date = date('Y-m-d H:i:s');
    $user_id = $_SESSION['id'];

    $fiat_id = getFiatIdByMethod($connection, $method_id);

    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && (heCan($user_data['role'], 1))) {

        $paydebt = $connection->query("UPDATE payments SET `sum` = `sum` - '$sum' WHERE client_debt_id = '$client_id' AND fiat_id = '$fiat_id'");

        if (!$paydebt)
            return error("failed");

        $add_ref = $connection->
        query("INSERT INTO debt_history (user_id, client_id, debt_sum, date, method_id) VALUES(\"$user_id\",\"$client_id\",\"$sum\",\"$date\", '$method_id') ");
        if ($add_ref && updateMethodMoney($connection, $method_id, $sum)) {
            echo json_encode(array("status" => "success"));
            return false;
        } else {
            return error("failed");
        }
    } else {
        return error("denied");
    }
} else {
    return error("empty");
}