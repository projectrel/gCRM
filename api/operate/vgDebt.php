<?php
if (isset($_POST['fiat_id'],$_POST['vg_id'], $_POST['currency_sum'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $vg_id = clean($_POST['vg_id']);
    $fiat_id = clean($_POST['fiat_id']);
    $currency_sum = clean($_POST['currency_sum']);
    session_start();
    $user_id = $_SESSION['id'];
    $user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
    if ($user_data && heCan($user_data['role'], 1)) {

        $update_vg_debt = $connection->query("
            UPDATE `payments` 
            SET `sum` = `sum` - $currency_sum 
            WHERE `vg_data_debt_id` = $vg_id AND fiat_id = $fiat_id
        ");
        if ($update_vg_debt) {
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