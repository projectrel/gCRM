<?php
if (!isset($_POST['login'], $_POST['password'], $_POST['vg_sum'], $_POST['debt'])) {
    echo array('status' => 'failed', 'error' => 'empty');
    return false;
}
include_once './funcs.php';
include_once '../../db.php';

error_reporting(E_ALL);
ini_set('display_errors', 'on');
session_start();
$login = clean($_POST['login']);
$debt = clean($_POST['debt']);
$password = clean($_POST['password']);
$sum_vg = clean($_POST['vg_sum']);
$vg_id = $_POST['vg_type'];
$fiat_id = $_SESSION['fiat_id'];

$user_id = mysqli_fetch_array($connection->
query("SELECT user_id FROM users 
       WHERE user_id IN 
       (SELECT user_id FROM clients WHERE login = '$login')"))['user_id'];

$ik_id = mysqli_fetch_array($connection->
query("SELECT ik_id FROM branch 
       WHERE branch_id IN 
       (SELECT branch_id FROM clients WHERE login = '$login')"))['ik_id'];

$client_id = mysqli_fetch_assoc($connection->
query("SELECT client_id FROM clients WHERE login = '$login' AND password = '$password'"))['client_id'];
if (isset($client_id, $user_id)) {
    $order_info = mysqli_fetch_array($connection->
    query("SELECT order_id, real_out_percent, IFNULL(callmaster,0) AS 'callmaster', `loginByVg` FROM orders  
                  WHERE client_id = '$client_id' AND vg_id = '$vg_id' ORDER BY date DESC LIMIT 1"));
    if (!$order_info) {
        echo json_encode(array("status" => "failed", "error" => "REQUEST_FAILED"));
        return false;
    }
    $order_id = $order_info['order_id'];
    $loginByVG = $order_info['loginByVg'];
    $callmaster = $order_info['callmaster'];
    $out = $order_info['real_out_percent'];
    $sumFiat = (int)$sum_vg * (int)$out/100;




    echo json_encode(array(
        'ik_co_id'=>$ik_id,
        'ik_pm_no'=>'213412',
        'ik_am'=> $sumFiat,
        'ik_cur'=>'uah',
        'ik_desc'=>'Оплата VG',
        'ik_x_sum_vg'=> $sum_vg,
        'ik_x_login'=> $login,
        'ik_x_debt'=> $debt,
        'ik_x_password'=>$password,
        'ik_x_vg_id '=> $vg_id ,
        'ik_x_fiat_id'=> $fiat_id,
    ));

    return false;
}