<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/funcs.php";
include_once("../../db.php");

if (!isset($_POST['client'], $_POST['sum_vg'], $_POST['out'], $_POST['method_id'],
    $_POST['vg'], $_POST['fiat'], $_POST['loginByVg'])) {
    return error("empty");
}


$sum_vg = clean($_POST['sum_vg']);
$login_by_vg = clean($_POST['loginByVg']);
$vg = clean($_POST['vg']);
$rollback_1 = $_POST['rollback_1'] ? clean($_POST['rollback_1']) : 0;
$client = clean($_POST['client']);
$callmaster = $_POST['callmaster'];
$description = $_POST['descr'];
$out_percent = clean($_POST['out']);
$method_id = $_POST['method_id'];

$sum_currency = ($sum_vg * $out_percent) / 100;
$rollback_sum = $sum_vg * $rollback_1 / 100;

$shares = is_array($_POST['shares']) ? $_POST['shares'] : json_decode($_POST['shares'], true);

$debt = $_POST['debtCl'] ? clean($_POST['debtCl']) : 0;

$money_to_add = $sum_currency - $debt;
$date = date('Y-m-d H:i:s');
$fiat = clean($_POST['fiat']);


session_start();
$user_id = $_POST['user_id'] ? $_POST['user_id'] : $_SESSION['id'];
$branch_id = $_SESSION['branch_id'];

$user_data = mysqli_fetch_assoc($connection->query("
        SELECT *
        FROM users
        WHERE user_id='$user_id'
    "));
if (!heCan($user_data['role'], 1)) {
    return error("denied");
}
$vg_id = mysqli_fetch_assoc($connection->query("
            SELECT vg_id
            FROM vg_data
            WHERE vg_data_id = '$vg'"))['vg_id'];
if ($callmaster) {
    $query="INSERT INTO `orders`
        (`vg_id`, `vg_data_id`, `client_id`, `sum_vg`, `real_out_percent`, `sum_currency`, `method_id`, `rollback_sum`, `rollback_1`, `date`, `callmaster`, `order_debt`, `description`, `fiat_id`, `loginByVg`)
        VALUES
        ('$vg_id ','$vg', '$client', '$sum_vg', '$out_percent', '$sum_currency','$method_id', '$rollback_sum', '$rollback_1', '$date', '$callmaster', '$debt', '$description', '$fiat', '$login_by_vg') ";
} else {
    $query="INSERT INTO `orders`
        (`vg_id`, `vg_data_id`, `client_id`, `sum_vg`, `real_out_percent`, `sum_currency`, `method_id`, `rollback_sum`, `rollback_1`, `date`, `order_debt`, `description`, `fiat_id`, `loginByVg`)
        VALUES
        ('$vg_id ', '$vg', '$client', '$sum_vg', '$out_percent', '$sum_currency','$method_id', '$rollback_sum', '$rollback_1', '$date', '$debt', '$description', '$fiat', '$login_by_vg') ";
		}


$add_order = $connection->
query($query);
if ($add_order) {
    $in_percent = mysqli_fetch_assoc($connection->query("
            SELECT in_percent
            FROM vg_data
            WHERE vg_data_id = '$vg'
            "))['in_percent'];

    $order_id = mysqli_fetch_assoc($connection->query("
            SELECT order_id
            FROM orders
            ORDER BY `date` DESC
            LIMIT 1
            "))['order_id'];

    if (!addShares($connection, $order_id, $shares, $out_percent, $in_percent, $rollback_1, $sum_vg)) {
        return error("SHARES_NOT_ADDED");
    }

    $participating_in_balance = mysqli_fetch_assoc($connection->query("
            SELECT participates_in_balance
            FROM methods_of_obtaining
            WHERE `method_id` = '$method_id'
            "))['participates_in_balance'];

    if ($debt > 0) addDebt($connection, $client, $fiat, $debt);
    if ($rollback_sum > 0) addRollback($connection, $fiat, $callmaster, $rollback_sum);
    if ($money_to_add > 0 && $participating_in_balance) updateBranchMoney($connection, $branch_id, $money_to_add, $fiat);

    $vg_data = mysqli_fetch_assoc($connection->query("
                SELECT `api_url_regexp` AS `url`, access_key AS `key`
                FROM vg_data
                WHERE vg_data_id = '$vg'
            "));
    $client_login = mysqli_fetch_assoc($connection->query("
                SELECT `byname`
                FROM clients
                WHERE client_id = '$client'
            "))['byname'];

    $vg_url = parse_vg_url($vg_data['url'], $sum_vg, $vg_data['key'], $login_by_vg);
    if (!$vg_url) {
        echo json_encode(array("status" => "success"));
        return false;
    }

    set_error_handler(
        function ($severity, $message, $file, $line) {
            throw new ErrorException($message, $severity, $severity, $file, $line);
        }

    );

    try {
        $result = json_decode(file_get_contents($vg_url));

        if ($result->{'success'} == false) {
            $result->{'url'} = $vg_url;
            $result->{'status'} = "success";
            echo json_encode($result);
            return false;
        }
    } catch (Exception $e) {
        $response['url'] = $vg_url;
        $response['success'] = false;
        $response['status'] = "success";
        echo json_encode($response);
        return false;
    }

    restore_error_handler();
    echo json_encode(array("status" => "success"));
    return false;
} else {
    return error("failed");
}


//FUNCTIONS
function parse_vg_url($vg_url_in, $sum_vg, $key, $login_by_vg)
{
    $vg_url = strtolower($vg_url_in);
    if (!isset($vg_url) || $vg_url == "" || $vg_url == " ") {

        return false;
    }

    if (strpos($vg_url, '%clientlogin%') && strpos($vg_url, '%sum%') && strpos($vg_url, '/api/transfer/?tr=%idtransact%&key=')) {
        $IDTransact = generateRandomString();
        $vg_url = str_replace("%sum%", $sum_vg, $vg_url);
        $vg_url = str_replace("%idtransact%", $IDTransact, $vg_url);
        $vg_url = str_replace("%key%", $key, $vg_url);
        $vg_url = str_replace("%clientlogin%", $login_by_vg, $vg_url);
        $nMidApi = strpos($vg_url_in, '/api/');
        $vg_url_4md5 = substr($vg_url, $nMidApi);
        $md5 = md5($vg_url_4md5 . ":" . $key);
        $vg_url = $vg_url . "&sign=" . $md5;
    } else {
        $vg_url = str_replace("%clientlogin%", $login_by_vg, $vg_url_in);
        $vg_url = str_replace("%sum%", $sum_vg, $vg_url);
    }
    return $vg_url;

}

function addShares($connection, $order_id, $shares, $out_percent, $in_percent, $rollback_1, $sum_vg)
{
    if(!$shares){
        return;
    }
    foreach ($shares as $key => $var) {
        $sum_of_owner = (($out_percent - $in_percent - $rollback_1) / 100) * ($sum_vg * ($var['value'] / 100));
        $curr_owner_id = $var['owner_id'];
        $share_percent = $var['value'];
        $add_share = $connection->
        query("INSERT INTO `shares`
                (`order_id`, `user_as_owner_id`, `sum`, `share_percent`) VALUES
                ('$order_id','$curr_owner_id','$sum_of_owner','$share_percent') ");
        if (!$add_share)
            return false;
    }
    return true;
}

function addDebt($connection, $client, $fiat, $debt)
{
    $check_payment_debt = mysqliToArray($connection->
    query("SELECT * FROM payments
                              WHERE `fiat_id` = '$fiat' AND `client_debt_id` = '$client' "));
    if ($check_payment_debt)
        $update_payments_debt = $connection->
        query("UPDATE  `payments`
                                  SET `sum` = `sum` + '$debt'
                                  WHERE client_debt_id = '$client' AND `fiat_id` = '$fiat'");
    else
        $insert_payments_debt = $connection->
        query("INSERT INTO `payments`
                             (`fiat_id`, `sum`, `client_debt_id`)
                             VALUES('$fiat', '$debt', '$client') ");
}

function addRollback($connection, $fiat, $callmaster, $rollback_sum)
{

    $check_payment_rollback = mysqliToArray($connection->
    query("SELECT * FROM payments
                              WHERE `fiat_id` = '$fiat' AND `client_rollback_id` = '$callmaster' "));

    if ($check_payment_rollback)
        $connection->
        query("UPDATE  `payments`
                                  SET `sum` = `sum` + '$rollback_sum'
                                  WHERE client_rollback_id = '$callmaster' AND `fiat_id` = '$fiat'");
    else
        $connection->
        query("INSERT INTO `payments`
                             (`fiat_id`, `sum`, `client_rollback_id`)
                             VALUES('$fiat', '$rollback_sum', '$callmaster') ");
}