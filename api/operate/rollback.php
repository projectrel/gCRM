<?php
if (isset($_POST['number']) && isset($_POST['id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");

    $client_id = explode('-', $_POST['id'])[0];
    $number = clean($_POST['number']);
    $fiat = clean($_POST['fiat']);
    session_start();
    $branch_id = $_SESSION['branch_id'];
    $date = date('Y-m-d H:i:s');
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && heCan($user_data['role'], 1)) {

        $change_rollback_sum = $connection->query("
            UPDATE `payments` 
            SET `sum` = `sum` - $number 
            WHERE `client_rollback_id` = $client_id AND fiat_id = $fiat
        ");
        if($change_rollback_sum){
            $add_ref = $connection->
            query("INSERT INTO rollback_paying (user_id, client_id, rollback_sum, date, fiat_id) VALUES(\"$user_id\",\"$client_id\",\"$number\",\"$date\", '$fiat') ");

           updateBranchMoney($connection, $branch_id, -$number, $fiat);

        }
        if ($add_ref) {
            echo json_encode(array("status" => "success"));
            return false;
        }else{
            return error("failed");
        }
    } else {
        return error("denied");
    }
} else {
    return error("failed");
}