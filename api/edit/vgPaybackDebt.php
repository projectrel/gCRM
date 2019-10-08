<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/funcs.php";
include_once("../../db.php");
if (!isset($_POST['vg_id'], $_POST['debt_sum'], $_POST['outgo_id'], $_POST['method_id'])) {
    return error("empty");
}
session_start();
$branch_id = $_SESSION['branch_id'];
$user_id = $_SESSION['id'];

$outgo_id = clean($_POST['outgo_id']);
$debt_sum = clean($_POST['debt_sum']);
$vg_id = clean($_POST['vg_id']);
$method_id = clean($_POST['method_id']);

if (!updateMethodAndDebtBalances($connection, $outgo_id, $vg_id, $method_id, $debt_sum)) {
    return error("failed");
}

if (!updateOutgo($connection, $outgo_id, $vg_id, $method_id, $debt_sum)) {
    return error("failed");
}

if (!save_change_info($connection, 'outgo', $outgo_id)) {
    return error("failed");
}


echo json_encode(array("status" => "success"));


function updateOutgo($connection, $outgo_id, $vg_id, $method_id, $debt_sum)
{
    return $connection->query("
            UPDATE `outgo` 
            SET `sum` =  '$debt_sum', `method_id` = '$method_id', `vg_data_id` = '$vg_id'
            WHERE `outgo_id` = '$outgo_id'
        ");
}

function updateMethodAndDebtBalances($connection, $outgo_id, $vg_id, $method_id, $debt_sum)
{
    $old_outgo = mysqli_fetch_assoc($connection->query("
           SELECT O.sum, O.method_id, O.vg_data_id, VD.branch_id FROM outgo O
           INNER JOIN vg_data VD ON O.vg_data_id = VD.vg_data_id
           WHERE O.outgo_id = '$outgo_id'
        "));
    $old_sum = $old_outgo['sum'];
    $old_method_id = $old_outgo['method_id'];
    $old_vg_id = $old_outgo['vg_data_debt_id'];
    $fiat_id = getFiatIdByMethod($connection, $method_id);

    if ($old_method_id == $method_id && $old_vg_id == $vg_id) {
        $update_debt_balance = $connection->query("
            UPDATE `payments` 
            SET `sum` =  `sum` + '$debt_sum' - '$old_sum'
            WHERE `fiat_id` = '$fiat_id' AND `vg_data_debt_id` = '$vg_id'
        ");
        $update_old_debt_balance = true;
        $update_old_method_balance = true;
        $update_method_balance = updateMethodMoney($connection, $old_method_id, $old_sum - $debt_sum);
    } else {
        //METHOD BALANCE
        $update_method_balance = $connection->query(" 
            UPDATE `payments` 
            SET `sum` = `sum` - $debt_sum 
            WHERE  method_id = '$method_id'");

        $update_old_method_balance = $connection->query(" 
            UPDATE `payments` 
            SET `sum` = `sum` + $old_sum 
            WHERE  method_id = '$old_method_id'");

        //DEBT BALANCE
        $update_old_debt_balance = $connection->query(" 
            UPDATE `payments` 
            SET `sum` = `sum` - $old_sum 
            WHERE `vg_data_debt_id` = '$vg_id' AND fiat_id = '$fiat_id'");

        $branch_vg_debt_balance_check_query = "SELECT * FROM `payments` WHERE `vg_data_debt_id` = '$vg_id' AND fiat_id = '$fiat_id' ";
        if ($connection->query($branch_vg_debt_balance_check_query)) {
            $update_debt_balance = $connection->query(" 
            UPDATE `payments` 
            SET `sum` = `sum` + $debt_sum 
            WHERE `vg_data_debt_id` = '$vg_id' AND fiat_id = '$fiat_id'");
        } else {
            $update_debt_balance = $connection->query("
            INSERT INTO `payments`
            (`fiat_id`, `sum`, `vg_data_debt_id`) 
            VALUES 
            ('$fiat_id','$debt_sum','$vg_id')");
        }
    }

    return $update_debt_balance && $update_old_debt_balance && $update_method_balance && $update_old_method_balance;
}