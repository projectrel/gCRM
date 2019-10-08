<?php
if (isset($_POST['sum'], $_POST['client_id'], $_POST['method_id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");

    $client_id = explode('-', $_POST['client_id'])[0];
    $sum = clean($_POST['sum']);
    $method_id = clean($_POST['method_id']);
    session_start();
    $branch_id = $_SESSION['branch_id'];
    $date = date('Y-m-d H:i:s');
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && heCan($user_data['role'], 1)) {
        $fiat_id = getFiatIdByMethod($connection, $method_id);
        $change_rollback_sum = $connection->query("
            UPDATE `payments` 
            SET `sum` = `sum` - $sum 
            WHERE `client_rollback_id` = $client_id AND fiat_id = $fiat_id
        ");
        if ($change_rollback_sum) {
            $add_ref = $connection->
            query("INSERT INTO rollback_paying (user_id, client_id, rollback_sum, date, method_id) VALUES(\"$user_id\",\"$client_id\",\"$sum\",\"$date\", '$method_id') ");

        }
        if ($add_ref &&  updateMethodMoney($connection, $method_id, -$sum)) {
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