<?php
if (isset($_POST['number']) && isset($_POST['id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");

    $fiat = clean($_POST['fiat']);
    $number = clean($_POST['number']);
    $client_id = explode('-', $_POST['id'])[0];
    session_start();
    $branch_id = $_SESSION['branch_id'];
    $date = date('Y-m-d H:i:s');
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && (heCan($user_data['role'], 1))) {

        $paydebt = $connection->query("UPDATE payments SET sum = sum - '$number' WHERE client_debt_id = '$client_id' AND fiat_id = '$fiat'");

        if($paydebt){
            $add_ref = $connection->
            query("INSERT INTO debt_history (user_id, client_id, debt_sum, date, fiat_id) VALUES(\"$user_id\",\"$client_id\",\"$number\",\"$date\", '$fiat') ");

            updateBranchMoney($connection, $branch_id, $number, $fiat);
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
    return error("empty");
}