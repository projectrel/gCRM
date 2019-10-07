<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/funcs.php";
include_once("../../db.php");
if (!isset($_POST['vg_id'], $_POST['debt_sum'], $_POST['outgo_id'], $_POST['fiat_id'])) {
    return error("empty");
}
session_start();
$branch_id = $_SESSION['branch_id'];
$user_id = $_SESSION['id'];

$outgo_id = clean($_POST['outgo_id']);
$debt_sum = clean($_POST['debt_sum']);
$vg_id = clean($_POST['vg_id']);
$fiat_id = clean($_POST['fiat_id']);

if (!updateBranchAndDebtBalances($connection, $outgo_id, $vg_id, $fiat_id, $debt_sum)) {
    return error("failed");
}

if (!updateOutgo($connection, $outgo_id, $vg_id, $fiat_id, $debt_sum)) {
    return error("failed");
}

if (!save_change_info($connection,'outgo',$outgo_id)) {
    return error("failed");
}


echo json_encode(array("status" => "success"));


function updateOutgo($connection, $outgo_id, $vg_id, $fiat_id, $debt_sum)
{
    return $connection->query("
            UPDATE `outgo` 
            SET `sum` =  '$debt_sum', `fiat_id` = '$fiat_id', `vg_data_id` = '$vg_id'
            WHERE `outgo_id` = '$outgo_id'
        ");
}

function updateBranchAndDebtBalances($connection, $outgo_id, $vg_id, $fiat_id, $debt_sum)
{
    $old_outgo = mysqli_fetch_assoc($connection->query("
           SELECT O.sum, O.fiat_id, O.vg_data_id, VD.branch_id FROM outgo O
           INNER JOIN vg_data VD ON O.vg_data_id = VD.vg_data_id
           WHERE O.outgo_id = '$outgo_id'
        "));
    $old_branch_id = $old_outgo['branch_id'];
    $old_sum = $old_outgo['sum'];
    $old_fiat_id = $old_outgo['fiat_id'];
    $old_vg_id = $old_outgo['vg_data_debt_id'];

    if ($old_fiat_id == $fiat_id && $old_vg_id == $vg_id) {
        $update_debt_balance = $connection->query("
            UPDATE `payments` 
            SET `sum` =  `sum` + '$debt_sum' - '$old_sum'
            WHERE `fiat_id` = '$fiat_id' AND `vg_data_debt_id` = '$vg_id'
        ");
        $update_branch_balance = $connection->query("
            UPDATE `payments` 
            SET `sum` =  `sum` - '$debt_sum' + '$old_sum'
            WHERE `fiat_id` = '$fiat_id' AND `branch_id` = '$old_branch_id'
        ");
    } else {
        $branch_fiat_balance_check_query = "SELECT * FROM `payments` WHERE `branch_id` = '$old_branch_id' AND fiat_id = '$fiat_id' ";
        if ($connection->query($branch_fiat_balance_check_query)) {
            $update_branch_balance = $connection->query(" 
            UPDATE `payments` 
            SET `sum` = `sum` - $debt_sum 
            WHERE branch_id = '$old_branch_id' AND fiat_id = '$fiat_id'");
        } else {
            $update_branch_balance = $connection->query("
            INSERT INTO `payments`
            (`fiat_id`, `sum`, `branch_id`) 
            VALUES 
            ('$fiat_id','-$debt_sum','$old_branch_id')");
        }

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

    return $update_debt_balance && $update_branch_balance;
}