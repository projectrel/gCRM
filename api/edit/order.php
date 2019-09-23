<?php
if (!(isset($_POST['order_id']) &&
    isset($_POST['client_id']) &&
    isset($_POST['sum_vg']) &&
    isset($_POST['out']) &&
    isset($_POST['method_id']) &&
    isset($_POST['vg_id']) &&
    isset($_POST['shares']) &&
    isset($_POST['fiat']) &&
    isset($_POST['client_id']))) {
    error("empty");
    return false;
}
include_once("../../db.php");
include_once("../../funcs.php");

$rollback_1 = isset($_POST['rollback_1']) ? clean($_POST['rollback_1']) : 0;

$vg_data_id = clean($_POST['vg_id']);
$order_id = clean($_POST['order_id']);
$client_id = clean($_POST['client_id']);
$sum_vg = clean($_POST['sum_vg']);
$description = $_POST['descr'];
$out_percent = clean($_POST['out']);
$debt = isset($_POST['debt']) ? clean($_POST['debt']) : 0;
$callmaster = clean($_POST['callmaster']);
$method_id = clean($_POST['method_id']);
$fiat = clean($_POST['fiat']);
$sum_currency = ($sum_vg * $out_percent) / 100;
$rollback_sum = $sum_vg * $rollback_1 / 100;
$shares = $_POST['shares'];
session_start();
$user_id = $_SESSION['id'];
$branch_id = $_SESSION['branch_id'];

$user_data = mysqli_fetch_assoc($connection->query("SELECT * FROM users WHERE user_id='$user_id'"));
if ($user_data && (heCan($user_data['role'], 2))) {
    $order_data = mysqli_fetch_assoc($connection->
    query("SELECT *
                     FROM orders
                     WHERE order_id ='$order_id'"));
    $old_shares_data = mysqliToArray($connection->
    query("SELECT user_as_owner_id AS `owner_id`, share_percent
                     FROM shares
                     WHERE order_id ='$order_id'"));
    $sharesChanged = true;

    if ($sharesChanged) {
        $connection->
        query("DELETE FROM shares
                     WHERE order_id ='$order_id'");

        $in_percent = mysqli_fetch_assoc($connection->query("
            SELECT in_percent
            FROM vg_data
            WHERE vg_data_id = '$vg_data_id'
            "))['in_percent'];
        foreach ($shares as $key => $value) {
            $curr_owner_id = $value['owner_id'];
            $share_percent = $value['value'];
            $sum_of_owner = (($out_percent - $in_percent - $rollback_1) / 100) * ($sum_vg * ($share_percent / 100));
            $add_share = $connection->
            query("INSERT INTO `shares`
                (`order_id`, `user_as_owner_id`, `sum`, `share_percent`) VALUES
                ('$order_id','$curr_owner_id','$sum_of_owner','$share_percent') ");
        }
    }
    $participates_in_balance = mysqli_fetch_assoc($connection->
    query("SELECT participates_in_balance
                     FROM methods_of_obtaining
                     WHERE method_id ='$method_id'"))['participates_in_balance'];

    $prevMethodId = $order_data['method_id'];
    $prev_method_participated = mysqli_fetch_assoc($connection->
    query("SELECT participates_in_balance
                     FROM methods_of_obtaining
                     WHERE method_id ='$prevMethodId'"))['participates_in_balance'];

    //TODO add logic of changing method
    if ((int)$prev_method_participated === (int)$participates_in_balance) {
        if ($order_data['sum_currency'] != $sum_currency) {
            $money = $sum_currency - $order_data['sum_currency'];
            if((int)$participates_in_balance){
                updateBranchMoney($connection, $branch_id, $money, $fiat);
            }

        }
    } else {
        if((int)$prev_method_participated === 1){
            updateBranchMoney($connection, $branch_id, - $order_data['sum_currency'], $fiat);
        }else{
            updateBranchMoney($connection, $branch_id, $order_data['sum_currency'], $fiat);
        }
    }

    if ($order_data['client_id'] != $client_id) {
        $old_client = $order_data['client_id'];
        $old_debt = $order_data['order_debt'];
        if ($order_data['order_debt'] != $debt) {
            $money = $debt - $old_debt;
            updateBranchMoney($connection, $branch_id, -$money, $fiat);
        }
        $update_old_client = $connection->
        query("UPDATE clients SET `debt` = `debt` - '$old_debt'
                     WHERE `client_id` = '$old_client'");

        $update_client = $connection->
        query("UPDATE clients SET `debt` = `debt` + '$debt'
                     WHERE `client_id` = '$client_id'");

    } else if ($order_data['order_debt'] != $debt) {
        $new_debt = $debt - $order_data['order_debt'];
        $update_debt = $connection->
        query("UPDATE clients SET `debt` = `debt` + '$new_debt'
                     WHERE `client_id` = $client_id");

        updateBranchMoney($connection, $branch_id, -$new_debt, $fiat);

    }
    if ($order_data['callmaster'] != $callmaster) {
        $old_callmaster = $order_data['callmaster'];
        $old_rollback = $order_data['rollback_sum'];

        $update_old_callmaster = $connection->
        query("UPDATE clients SET `rollback_sum` = `rollback_sum` - '$old_rollback'
                     WHERE `client_id` = '$old_callmaster'");

        $update_callmaster = $connection->
        query("UPDATE clients SET `rollback_sum` = `rollback_sum` + '$rollback_sum'
                     WHERE `client_id` = '$callmaster'");

    } else if ($order_data['rollback_sum'] != $rollback_sum) {
        $new_rollback = $order_data['rollback_sum'] > $rollback_sum ? -($order_data['rollback_sum'] - $rollback_sum) : $rollback_sum - $order_data['rollback_sum'];
        $update_rollback = $connection->
        query("UPDATE clients SET `rollback_sum` = `rollback_sum` + '$new_rollback'
                     WHERE `client_id` = $callmaster");
    }
    if ($callmaster) {
        $res = $connection->
        query("UPDATE orders SET `vg_data_id` = '$vg_data_id',
                     `client_id` = '$client_id',`sum_vg` = '$sum_vg',`real_out_percent` = '$out_percent',
                     `sum_currency` = '$sum_currency',`order_debt` = '$debt',`method_id` = '$method_id',
                     `rollback_sum` = '$rollback_sum',`rollback_1` = '$rollback_1',
                     `callmaster` = '$callmaster', `description` = '$description', `fiat_id` = '$fiat'
                     WHERE `order_id` = $order_id");
    } else {
        $res = $connection->
        query("UPDATE orders SET `vg_data_id` = '$vg_data_id',
                     `client_id` = '$client_id',`sum_vg` = '$sum_vg',`real_out_percent` = '$out_percent',
                     `sum_currency` = '$sum_currency',`order_debt` = '$debt',`method_id` = '$method_id', `description` = '$description', `fiat_id` = '$fiat'
                     WHERE `order_id` = $order_id");
    }

    if ($res) {
        if ($order_data['sum_vg'] != $sum_vg)
            echo json_encode(array('status' => 'edit-success', 'sumChanged' => true, 'oldSum' => $order_data['sum_vg'], 'newSum' => $sum_vg));
        else
            echo json_encode(array('status' => "edit-success"));
        return false;
    } else {
        error("failed");
        return false;
    }

} else {
    error("denied");
    return false;

}


