<?php
include_once("../../funcs.php");
if (!(isset($_POST['order_id'], $_POST['client_id'], $_POST['sum_vg'], $_POST['out'], $_POST['method_id'], $_POST['vg_id'], $_POST['shares'],
    $_POST['fiat'], $_POST['client_id'], $_POST['sum_manually'], $_POST['enter_manually']))) {
    error("empty");
    return false;
}
include_once("../../db.php");


$rollback_1 = isset($_POST['rollback_1']) ? clean($_POST['rollback_1']) : 0;

$vg_data_id = clean($_POST['vg_id']);
$order_id = clean($_POST['order_id']);
$client_id = clean($_POST['client_id']);
$sum_vg = clean($_POST['sum_vg']);
$description = $_POST['descr'];
$out_percent = clean($_POST['out']);
$debt = isset($_POST['debt']) ? clean($_POST['debt']) : 0;
$callmaster = $_POST['callmaster'] == -1 ? false : $_POST['callmaster'];
$method_id = clean($_POST['method_id']);
$fiat = clean($_POST['fiat']);
$sum_currency = $_POST['enter_manually'] === true || $_POST['enter_manually'] === "true" ? $_POST['sum_manually'] : ($sum_vg * $out_percent) / 100;
$in_percent = mysqli_fetch_assoc($connection->query("
            SELECT in_percent
            FROM vg_data
            WHERE vg_data_id = '$vg'"))['in_percent'];
$rollback_sum = $_POST['enter_manually'] === true || $_POST['enter_manually'] === "true" ? $sum_manually * ($rollback_1 / 100) :
    (($out_percent - $in_percent) / 100) * ($sum_vg) - (($out_percent - $in_percent - $rollback_1) / 100) * ($sum_vg);
$shares = $_POST['shares'];
if(!isset($_SESSION))
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
    if (!save_change_info($connection, "order", $order_id))
        error("failed");
    if ($order_data['vg_data_id'] != $vg_data_id || $order_data['sum_vg'] != $sum_vg) {
        if (!updateVgBalance($connection, $order_data, $vg_data_id, $sum_vg)) {
            return error("failed");
        }
    }
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
            if ((int)$participates_in_balance) {
                if (!updateMethodMoney($connection, $method_id, $money))
                    return error("custom", "Не удалось обновить деньги на счету");
            }

        }
    } else {
        if ((int)$prev_method_participated === 1) {
            if (!updateMethodMoney($connection, $method_id, -$order_data['sum_currency']))
                return error("custom", "Не удалось обновить деньги на счету");
        } else {
            if (!updateMethodMoney($connection, $method_id, $order_data['sum_currency']))
                return error("custom", "Не удалось обновить деньги на счету");
        }
    }
    $old_fiat_id = $order_data['fiat_id'];
    if ($order_data['client_id'] != $client_id || $order_data['order_debt'] != $debt || $old_fiat_id == $fiat) {
        if (!$connection->
        query("SELECT * FROM `payments`
                     WHERE `client_debt_id` = '$client_id' AND `fiat_id` = '$fiat'")) {
            $connection->
            query("INSERT INTO `payments` (`client_debt_id`, `fiat_id`,`sum`)
                          VALUES ('$client_id','$fiat',0)");
        }
        $old_client = $order_data['client_id'];
        $old_debt = $order_data['order_debt'];
        $update_old_client_debt = $connection->
        query("UPDATE payments SET `sum` = `sum` - '$old_debt'
                     WHERE `client_debt_id` = '$old_client' AND `fiat_id` = '$old_fiat_id'");
        $update_client_debt = $connection->
        query("UPDATE payments SET `sum` = `sum` + '$debt'
                     WHERE `client_debt_id` = '$client_id' AND `fiat_id` = '$fiat'");
        if (!($update_old_client_debt && $update_client_debt)) {
            return error("failed");
        }
    }


    if ($order_data['callmaster'] != $callmaster || $order_data['rollback_sum'] != $callmaster || $old_fiat_id == $fiat) {
        if (!$connection->
        query("SELECT * FROM `payments`
                     WHERE `client_rollback_id` = '$client_id' AND `fiat_id` = '$fiat'")) {
            $connection->
            query("INSERT INTO `payments` (`client_rollback_id`, `fiat_id`,`sum`)
                          VALUES ('$client_id','$fiat',0)");
        }
        $old_callmaster = $order_data['callmaster'];
        $old_rollback = $order_data['rollback_sum'];

        $update_old_callmaster = $connection->
        query("UPDATE payments SET `sum` = `sum` - '$old_rollback'
                     WHERE `client_rollback_id` = '$old_callmaster'  AND `fiat_id` = '$old_fiat_id'");

        $update_callmaster = $connection->
        query("UPDATE payments SET `sum` = `sum` + '$rollback_sum'
                     WHERE `client_rollback_id` = '$callmaster' AND `fiat_id` = '$fiat'");

        if (!($update_old_callmaster && $update_callmaster)) {
            return error("failed");

        }

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

function updateVgBalance($connection, $old_order_data, $vg_data_id, $sum_vg)
{
    $old_vg_sum = $old_order_data['sum_vg'];
    $old_vg_data_id = $old_order_data['vg_data_id'];
    return $connection->
        query("UPDATE vg_data SET `vg_amount` = `vg_amount` + '$old_vg_sum' WHERE `vg_data_id` = $old_vg_data_id") &&
        $connection->
        query("UPDATE vg_data SET `vg_amount` = `vg_amount` - '$sum_vg' WHERE `vg_data_id` = $vg_data_id");
}


