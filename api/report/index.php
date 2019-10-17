<?php
if (!isset($_GET['branch']))
    return false;

require '../../vendor/autoload.php';
include_once '../../db.php';
include_once '../../funcs.php';
include_once './reportConfig.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$branch_id = $_GET['branch'];
$current_row = 1;

$owners = mysqliToArray($connection->query("SELECT concat(first_name,' ',last_name) AS `name`, user_id AS id FROM `users` WHERE is_owner = 1 AND branch_id = '$branch_id'"));
$vg_fiat_in_branch_combinations_names = mysqliToArray($connection->
query("SELECT DISTINCT  concat(VD.name,' ', F.name) AS `name`
              FROM vg_data VD 
              CROSS JOIN fiats F
              WHERE branch_id = '$branch_id'"));
$vg_fiat_in_branch_combinations_ids = mysqliToArray($connection->
query("SELECT DISTINCT  VD.vg_data_id, F.fiat_id
              FROM vg_data VD 
              CROSS JOIN fiats F
              WHERE branch_id = '$branch_id'"));
$users_id = [];
$headers1 = FIRST_SECTION_HEADERS;
$headers2 = SECOND_SECTION_HEADERS;
$headers3 = THIRD_SECTION_HEADERS;
$headers4 = FOURTH_SECTION_HEADERS;


$last_vg_weekly_report_date = getLastVgReportDate($connection);
$report_vg_date = $last_vg_weekly_report_date;
$date = date(DATE_FORMAT);
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();


//FIRST SECTION
if (is_array($owners)) {
    $owners_id = array_column($owners, 'id');
    $headers1 = array_merge($headers1, array_column($owners, 'name'));
}
$vg_fiat_in_branch_combinations_data = array_column($vg_fiat_in_branch_combinations_names, 'name');
forEach ($vg_fiat_in_branch_combinations_data as $key => $value) {
    $fiat_id = $vg_fiat_in_branch_combinations_ids[$key]['fiat_id'];
    $vg_data_id = $vg_fiat_in_branch_combinations_ids[$key]['vg_data_id'];
    $vg_fiat_in_branch_combinations_data[$key] =
        array(
            1 => $value,
            2 => getVgBalancePrev($connection, $fiat_id, $vg_data_id, $report_vg_date),
            3 => getVgApiBalancePrev($connection, $fiat_id, $vg_data_id, $report_vg_date),
            4 => getVgDebtPrev($connection, $fiat_id, $vg_data_id, $report_vg_date),
            5 => getVgBalanceCurr($connection, $vg_data_id),
            6 => getVgApiBalanceCurr($connection, $vg_data_id),
            7 => getVgBoughtAmount($connection, $fiat_id, $vg_data_id, $report_vg_date),
            8 => getVgDebt($connection, $fiat_id, $vg_data_id),
            9 => getVgSold($connection, $fiat_id, $vg_data_id, $report_vg_date),
            10 => getVgSoldInFiat($connection, $fiat_id, $vg_data_id, $report_vg_date),
            11 => getVgRollback($connection, $fiat_id, $vg_data_id, $report_vg_date));
    foreach ($owners_id as $key2 => $owner_id) {
        array_push($vg_fiat_in_branch_combinations_data[$key], getVgOwnerProfit($connection, $fiat_id, $vg_data_id, $owner_id, $report_vg_date));
    }
}

//ADDING SECTIONS
setUpSheet($sheet);
addSection($sheet, FIRST_SECTION_TITLE, $headers1, $vg_fiat_in_branch_combinations_data, FIRST_SECTION_COLOR);


//SAVE SHEET
$writer = new Xlsx($spreadsheet);
$writer->save(TARGET_DIR . $date . '.xlsx');

//HELP FUNC
function checkIfNull($data)
{
    if (!$data || empty($data))
        return EMPTY_CELL;
    else
        return $data;
}


//SHEET FUNC
function addSection($sheet, $title, $headers, $content, $color = "FFFFFF")
{
    $sheet->setCellValueByColumnAndRow(1, $GLOBALS['current_row'], $title);
    $GLOBALS['current_row']++;
    $sheet->getRowDimension($GLOBALS['current_row'])->setRowHeight(ROW_HEIGHT);
    for ($i = 2; $i <= 2 + count($headers); $i++) {
        $sheet->getStyleByColumnAndRow($i,$GLOBALS['current_row'])->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF0000');
        $sheet->getStyleByColumnAndRow($i, $GLOBALS['current_row'])->getAlignment()->setWrapText(true);
        $sheet->setCellValueByColumnAndRow($i, $GLOBALS['current_row'], $headers[$i - 2]);

    }
    $GLOBALS['current_row']++;
    for ($i = 1; $i <= count($content); $i++) {
        for ($j = 1; $j <= count($content[$i - 1]); $j++) {
            $sheet->getStyleByColumnAndRow($j,$GLOBALS['current_row'])->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFFF0000');
            $sheet->setCellValueByColumnAndRow($j, $GLOBALS['current_row'], $content[$i - 1][$j]);
        }
        $GLOBALS['current_row']++;
    }
    $GLOBALS['current_row'] += 3;
}

function setUpSheet($sheet)
{
    $columns = EFFECTED_COLUMNS;
    foreach ($columns as $key => $var)
        if ($var == 'A')
            $sheet->getColumnDimension($var)->setWidth(COLUMN_WIDTH * 2);
        else
            $sheet->getColumnDimension($var)->setWidth(COLUMN_WIDTH);
}


//FIRST SEC FUNC
function getLastVgReportDate($connection)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT `vg_processing_report_date` 
FROM `vg_processing_reports`
WHERE `is_weekly` = 1
ORDER BY `vg_processing_report_date` DESC 
LIMIT 1"))['vg_processing_report_date']);
}

function getVgBalancePrev($connection, $fiat_id, $vg_data_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT `vg_balance` 
FROM `vg_processing_reports`
WHERE vg_data_id = '$vg_data_id' AND fiat_id = '$fiat_id' AND vg_processing_report_date = '$prev_report_date'"))['vg_balance']);
}

function getVgApiBalancePrev($connection, $fiat_id, $vg_data_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT `vg_api_balance` 
FROM `vg_processing_reports`
WHERE vg_data_id = '$vg_data_id' AND fiat_id = '$fiat_id' AND vg_processing_report_date = '$prev_report_date'"))['vg_api_balance']);
}

function getVgDebtPrev($connection, $fiat_id, $vg_data_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT `fiat_debt` 
FROM `vg_processing_reports`
WHERE vg_data_id = '$vg_data_id' AND fiat_id = '$fiat_id' AND vg_processing_report_date = '$prev_report_date'"))['fiat_debt']);
}

function getVgBalanceCurr($connection, $vg_data_id)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT `vg_amount` 
FROM `vg_data`
WHERE vg_data_id = '$vg_data_id'"))['vg_amount']);
}

function getVgApiBalanceCurr($connection, $vg_data_id)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT `vg_api_amount` 
FROM `vg_data`
WHERE vg_data_id = '$vg_data_id'"))['vg_amount']);
}

function getVgBoughtAmount($connection, $fiat_id, $vg_data_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT SUM(vg_purchase_sum) AS `sum` FROM `vg_purchases` 
WHERE `fiat_id` = '$fiat_id' AND `vg_data_id` = '$vg_data_id' AND `date` > '$prev_report_date'
GROUP BY `fiat_id`, `vg_data_id`"))['sum']);
}

function getVgDebt($connection, $fiat_id, $vg_data_id)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT  P.sum AS `sum`
FROM vg_data VD 
INNER JOIN payments P ON P.vg_data_debt_id = VD.vg_data_id
INNER JOIN fiats F ON F.fiat_id = P.fiat_id
WHERE  P.sum > 0  AND VD.vg_data_id = '$vg_data_id' AND F.fiat_id = '$fiat_id'"))['sum']);
}

function getVgSold($connection, $fiat_id, $vg_data_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT SUM(`sum_vg`) AS `sum` 
FROM `orders` 
WHERE `fiat_id` = '$fiat_id' AND `vg_data_id` = '$vg_data_id' AND `date` > '$prev_report_date'
GROUP BY fiat_id, vg_data_id"))['sum']);
}

function getVgSoldInFiat($connection, $fiat_id, $vg_data_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT SUM(`sum_currency`) AS `sum` 
FROM `orders` 
WHERE `fiat_id` = '$fiat_id' AND `vg_data_id` = '$vg_data_id' AND `date` > '$prev_report_date'
GROUP BY fiat_id, vg_data_id"))['sum']);
}

function getVgRollback($connection, $fiat_id, $vg_data_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT SUM(`rollback_sum`) AS `sum` 
FROM `orders` 
WHERE `fiat_id` = '$fiat_id' AND `vg_data_id` = '$vg_data_id' AND `date` > '$prev_report_date'
GROUP BY fiat_id, vg_data_id"))['sum']);
}

function getVgOwnerProfit($connection, $fiat_id, $vg_data_id, $owner_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("SELECT SUM(IFNULL(S.sum, 0)) AS `sum` 
FROM `orders` O 
LEFT JOIN `shares` S ON S.order_id = O.order_id
WHERE O.vg_data_id = '$vg_data_id' AND S.user_as_owner_id = '$owner_id' AND O.fiat_id = '$fiat_id' AND O.date > '$prev_report_date'
GROUP BY S.user_as_owner_id, O.vg_data_id, O.fiat_id"))['sum']);
}