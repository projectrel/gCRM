<?php
if (!isset($_GET['branch'], $_GET['weekly']))
    return false;

require '../../vendor/autoload.php';
include_once '../../db.php';
include_once '../../funcs.php';
include_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";
include_once './reportConfig.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$is_weekly = $_GET['weekly'] === true || $_GET['weekly'] === "true" || $_GET['weekly'] === 1 || $_GET['weekly'] === "1" ? 1 : 0;
$branch_id = $_GET['branch'];
$current_row = 1;

$vg_purchase_outgo_type_id = VG_PURCHASE_TYPE;


$owners = mysqliToArray($connection->query("SELECT concat(first_name,' ',last_name) AS `name`, user_id AS id, IFNULL(email,'') AS `email` FROM `users` WHERE is_owner = 1 AND branch_id = '$branch_id'"));
$vg_fiat_in_branch_combinations_data_select = mysqliToArray($connection->
query("SELECT DISTINCT  concat(VD.name,', ', F.name) AS `name`, VD.vg_data_id, F.fiat_id
              FROM `vg_data` VD 
              CROSS JOIN `fiats` F
              WHERE `branch_id` = '$branch_id'"));

$outgo_method_fiat_in_branch_combinations_data_select = mysqliToArray($connection->
query("SELECT `outgo_type_name`, MOOT.method_name, MOOT.outgo_type_id, MOOT.method_id, F.name AS `fiat_name`
FROM           (SELECT DISTINCT  OT.outgo_name AS `outgo_type_name`, MOO.method_name AS `method_name`, OT.outgo_type_id, MOO.method_id 
               FROM `outgo_types` OT
               CROSS JOIN `methods_of_obtaining` MOO
               WHERE (OT.branch_id = '$branch_id' AND MOO.branch_id = '$branch_id') OR OT.outgo_type_id = '$vg_purchase_outgo_type_id')  MOOT 
               INNER JOIN `payments` P ON P.method_id = MOOT.method_id
              INNER JOIN `fiats` F ON P.fiat_id = F.fiat_id
            "));

$method_data_select = mysqliToArray($connection->
query("SELECT `method_name`, `method_id`
              FROM `methods_of_obtaining`
              WHERE `branch_id` = '$branch_id'
            "));


$fiat_data_select = mysqliToArray($connection->
query("SELECT `fiat_id`, `name` AS 'fiat_name'
              FROM `fiats`
            "));


$users_id = [];
$headers1 = FIRST_SECTION_HEADERS;
$headers2 = SECOND_SECTION_HEADERS;
$headers3 = THIRD_SECTION_HEADERS;
$headers4 = FOURTH_SECTION_HEADERS;
$headers5 = FIFTH_SECTION_HEADERS;

//LAST REPORTS DATE
$last_report_date = getLastReportDate($connection, $is_weekly);


$date = date(DATE_FORMAT);
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();


//FIRST SECTION
if (is_array($owners)) {
    $owners_id = array_column($owners, 'id');
    $headers1 = array_merge($headers1, array_column($owners, 'name'));
}
$vg_processing_data = array_column($vg_fiat_in_branch_combinations_data_select, 'name');
forEach ($vg_processing_data as $key => $value) {
    $fiat_id = $vg_fiat_in_branch_combinations_data_select[$key]['fiat_id'];
    $vg_data_id = $vg_fiat_in_branch_combinations_data_select[$key]['vg_data_id'];
    $vg_processing_data[$key] =
        array(
            1 => $value,
            2 => getVgBalancePrev($connection, $fiat_id, $vg_data_id, $last_report_date),
            3 => getVgApiBalancePrev($connection, $fiat_id, $vg_data_id, $last_report_date),
            4 => getVgDebtPrev($connection, $fiat_id, $vg_data_id, $last_report_date),
            5 => getVgBalanceCurr($connection, $vg_data_id),
            6 => getVgApiBalanceCurr($connection, $vg_data_id),
            7 => getVgBoughtAmount($connection, $fiat_id, $vg_data_id, $last_report_date),
            8 => getVgDebt($connection, $fiat_id, $vg_data_id),
            9 => getVgSold($connection, $fiat_id, $vg_data_id, $last_report_date),
            10 => getVgSoldInFiat($connection, $fiat_id, $vg_data_id, $last_report_date),
            11 => getVgRollback($connection, $fiat_id, $vg_data_id, $last_report_date));
    foreach ($owners_id as $key2 => $owner_id) {
        array_push($vg_processing_data[$key], getVgOwnerProfit($connection, $fiat_id, $vg_data_id, $owner_id, $last_report_date));
    }
}


//SECOND SECTION
$outgo_processing_data = array();
forEach ($outgo_method_fiat_in_branch_combinations_data_select as $key => $value) {
    $method_id = $outgo_method_fiat_in_branch_combinations_data_select[$key]['method_id'];
    $outgo_type_id = $outgo_method_fiat_in_branch_combinations_data_select[$key]['outgo_type_id'];
    $outgo_processing_data[$key] =
        array(
            1 => $outgo_method_fiat_in_branch_combinations_data_select[$key]['outgo_type_name'],
            2 => $outgo_method_fiat_in_branch_combinations_data_select[$key]['method_name'],
            3 => $outgo_method_fiat_in_branch_combinations_data_select[$key]['fiat_name'],
            4 => getOutgoesSums($connection, $outgo_type_id, $method_id, $last_report_date)
        );
}

//THIRD SECTION
$method_processing_data = array();
forEach ($method_data_select as $key => $value) {
    $method_id = $method_data_select[$key]['method_id'];
    $outgo = getMethodOutgo($connection, $method_id, $last_report_date);
    $income = getMethodIncome($connection, $method_id, $last_report_date);
    $diff = $income === EMPTY_CELL ? 0 : $income;
    $diff = $outgo === EMPTY_CELL ? $diff : $diff - $outgo;
    $method_processing_data[$key] =
        array(
            1 => $method_data_select[$key]['method_name'],
            2 => getFiatAmountCurr($connection, $method_id),
            3 => getFiatAmountPrev($connection, $method_id, $last_report_date),
            4 => $outgo,
            5 => $income,
            6 => $diff,
        );
}

//FOURTH SECTION
$debt_processing_data = array_column($vg_fiat_in_branch_combinations_data_select, 'name');
forEach ($debt_processing_data as $key => $value) {
    $fiat_id = $vg_fiat_in_branch_combinations_data_select[$key]['fiat_id'];
    $vg_data_id = $vg_fiat_in_branch_combinations_data_select[$key]['vg_data_id'];
    $debt_processing_data[$key] =
        array(
            1 => $value,
            2 => getVgFiatDebt($connection, $vg_data_id, $fiat_id)
        );
}

//FIFTH SECTION
$fiat_processing_data = array_column($fiat_data_select, 'fiat_name');
forEach ($fiat_processing_data as $key => $value) {
    $fiat_id = $fiat_data_select[$key]['fiat_id'];
    $fiat_processing_data[$key] =
        array(
            1 => $value,
            2 => getFiatDebt($connection, $branch_id, $fiat_id),
            3 => getFiatRollback($connection, $branch_id, $fiat_id),
            4 => getFiatProfit($connection, $branch_id, $fiat_id),

        );
}


//ADDING SECTIONS
setUpSheet($sheet);
addSection($sheet, FIRST_SECTION_TITLE, $headers1, $vg_processing_data, FIRST_SECTION_COLOR);
addSection($sheet, SECOND_SECTION_TITLE, $headers2, $outgo_processing_data, SECOND_SECTION_COLOR);
addSection($sheet, THIRD_SECTION_TITLE, $headers3, $method_processing_data, THIRD_SECTION_COLOR);
addSection($sheet, FOURTH_SECTION_TITLE, $headers4, $debt_processing_data, FOURTH_SECTION_COLOR);
addSection($sheet, FIFTH_SECTION_TITLE, $headers5, $fiat_processing_data, FOURTH_SECTION_COLOR);


//SAVING SHEET
$writer = new Xlsx($spreadsheet);
$file_name = $is_weekly ? TARGET_DIR . "weekly_" . $date . '.xlsx' : TARGET_DIR . $date . '.xlsx';
$writer->save($file_name);


//SAVING TO DB
$unique_key = md5(uniqid(rand(), true));;
if (!addReport($connection, $unique_key, $is_weekly))
    return false;
addVgReport($connection, $unique_key, $vg_fiat_in_branch_combinations_data_select, $vg_processing_data);
addOutgoReport($connection, $unique_key, $outgo_method_fiat_in_branch_combinations_data_select, $outgo_processing_data);
addMethodReport($connection, $unique_key, $method_data_select, $method_processing_data);
addDebtReport($connection, $unique_key, $vg_fiat_in_branch_combinations_data_select, $debt_processing_data);
addFiatReport($connection, $unique_key, $fiat_processing_data);

echo json_encode(array('success' => true));
return false;
//SENDING TO OWNERS
$owners_emails = array_filter(array_column($owners, "email"), "stringIsNotEmpty");

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host = $_SERVER['DOCUMENT_ROOT'];                    // Set the SMTP server to send through
    $mail->SMTPAuth = true;                                     // Enable SMTP authentication
    //$mail->Username = '';                                     // SMTP username
    // $mail->Password = '';                                    // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
    //$mail->Port = 587;                                          // TCP port to connect to

    //Recipients
    $mail->setFrom('from@example.com', 'Mailer');
    foreach ($owners_emails as $key => $val) {
        $mail->addAddress($val);
    }
    $mail->addReplyTo('info@example.com', 'Information');

    // Attachments
    $mail->addAttachment($file_name, 'report.xlsx');         // Add attachments

    // Content
    $mail->isHTML(false);                                  // Set email format to HTML
    $mail->Subject = 'Отчет';
    $mail->Body = 'Отчет';
    $mail->AltBody = 'Отчет';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}


//HELP FUNC
function checkIfNull($data)
{
    if (!$data || empty($data))
        return EMPTY_CELL;
    else
        return $data;
}

function checkIfNullDate($date)
{
    if (!$date || is_null($date))
        return 0;
    else
        return $date;
}

function getLastReportDate($connection, $is_weekly)
{
    return checkIfNullDate(mysqli_fetch_assoc($connection->
    query("
SELECT `report_date` AS 'date'
FROM `reports`
WHERE `is_weekly` = '$is_weekly'
ORDER BY `report_date` DESC 
LIMIT 1"))['date']);
}

function stringIsNotEmpty($str)
{
    return !empty($str) && $str != "";
}

//SHEET FUNC
function addSection($sheet, $title, $headers, $content, $color = "FFFFFF")
{
    $sheet->setCellValueByColumnAndRow(1, $GLOBALS['current_row'], $title);
    $GLOBALS['current_row']++;
    $sheet->getRowDimension($GLOBALS['current_row'])->setRowHeight(ROW_HEIGHT);
    for ($i = 1; $i <= 1 + count($headers); $i++) {
        $sheet->getStyleByColumnAndRow($i - 1, $GLOBALS['current_row'])->applyFromArray(getSheetStyles($color));
        $sheet->getStyleByColumnAndRow($i, $GLOBALS['current_row'])->getAlignment()->setWrapText(true);
        $sheet->setCellValueByColumnAndRow($i, $GLOBALS['current_row'], $headers[$i - 1]);

    }
    $GLOBALS['current_row']++;
    for ($i = 1; $i <= count($content); $i++) {
        for ($j = 1; $j <= count($content[$i - 1]); $j++) {
            $sheet->getStyleByColumnAndRow($j, $GLOBALS['current_row'])->applyFromArray(getSheetStyles($color));
            $sheet->setCellValueByColumnAndRow($j, $GLOBALS['current_row'], $content[$i - 1][$j]);
        }
        $GLOBALS['current_row']++;
    }
    $GLOBALS['current_row'] += 3;
}

function getSheetStyles($color)
{
    return [
        'font' => [
            'bold' => true,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'left' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'right' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'bottom' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'rotation' => 90,
            'startColor' => [
                'argb' => $color,
            ],
            'endColor' => [
                'argb' => $color,
            ],
        ],
    ];
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
function getVgBalancePrev($connection, $fiat_id, $vg_data_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT `vg_balance` 
FROM `vg_processing_reports` VPR INNER JOIN `reports` R ON R.report_unique_key = VPR.report_unique_key
WHERE vg_data_id = '$vg_data_id' AND fiat_id = '$fiat_id' AND R.report_date = '$prev_report_date'"))['vg_balance']);
}

function getVgApiBalancePrev($connection, $fiat_id, $vg_data_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT `vg_api_balance` 
FROM `vg_processing_reports` VPR INNER JOIN `reports` R ON R.report_unique_key = VPR.report_unique_key
WHERE vg_data_id = '$vg_data_id' AND fiat_id = '$fiat_id' AND R.report_date = '$prev_report_date'"))['vg_api_balance']);
}

function getVgDebtPrev($connection, $fiat_id, $vg_data_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
SELECT `fiat_debt` 
FROM `vg_processing_reports` VPR INNER JOIN `reports` R ON R.report_unique_key = VPR.report_unique_key
WHERE vg_data_id = '$vg_data_id' AND fiat_id = '$fiat_id'  AND R.report_date = '$prev_report_date'"))['fiat_debt']);
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
SELECT SUM(vg_purchase_sum) AS `sum` 
FROM `vg_purchases` 
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

//SECOND SEC FUNC
function getOutgoesSums($connection, $outgo_type_id, $method_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
    SELECT SUM(IFNULL(O.sum, 0)) AS `sum` 
    FROM `outgo` O
    INNER JOIN `methods_of_obtaining` MOO ON MOO.method_id = O.method_id
    WHERE O.outgo_type_id = '$outgo_type_id' AND MOO.method_id = '$method_id' AND O.date > '$prev_report_date'
    GROUP BY O.outgo_type_id, MOO.method_id
"))['sum']);
}


//THIRD SEC FUNC
function getFiatAmountCurr($connection, $method_id)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
    SELECT `sum`
    FROM payments
    WHERE method_id = '$method_id' 
"))['sum']);
}

function getFiatAmountPrev($connection, $method_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
    SELECT `methods_processing_fiat_amount` AS `sum` 
    FROM `methods_processing_reports` MPR
     INNER JOIN `reports` R ON R.report_unique_key = MPR.report_unique_key
    WHERE MPR.method_id = '$method_id' AND R.report_date = '$prev_report_date'
"))['sum']);
}

function getMethodOutgo($connection, $method_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
    SELECT SUM(O.sum) AS  `sum` 
    FROM `outgo` O
    WHERE O.method_id = '$method_id' AND O.date > '$prev_report_date'
"))['sum']);
}

function getMethodIncome($connection, $method_id, $prev_report_date)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
    SELECT SUM(O.sum_currency - O.order_debt + IFNULL(DH.debt_sum,0)) AS  `sum` 
    FROM `orders` O
    LEFT JOIN `debt_history` DH ON DH.method_id = O.method_id
    WHERE O.method_id = '$method_id' AND O.date > '$prev_report_date'
    GROUP BY O.method_id
"))['sum']);
}


//FOURTH SEC FUN
function getVgFiatDebt($connection, $vg_data_id, $fiat_id)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
    SELECT `sum` 
    FROM `payments`
    WHERE vg_data_debt_id = '$vg_data_id' AND fiat_id ='$fiat_id'
"))['sum']);
}


//FIFTH SEC FUN
function getFiatDebt($connection, $branch_id, $fiat_id)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
    SELECT SUM(P.sum) AS `sum` 
    FROM `payments` P
    WHERE  fiat_id ='$fiat_id' AND `client_debt_id` IN 
    (SELECT `client_id` FROM `clients` WHERE user_id IN (SELECT `user_id` FROM `users` WHERE `branch_id` = '$branch_id'))
"))['sum']);
}

function getFiatRollback($connection, $branch_id, $fiat_id)
{
    return checkIfNull(mysqli_fetch_assoc($connection->
    query("
    SELECT SUM(P.sum) AS `sum` 
    FROM `payments` P
    WHERE  fiat_id ='$fiat_id' AND `client_rollback_id` IN 
    (SELECT `client_id` FROM `clients` WHERE user_id IN (SELECT `user_id` FROM `users` WHERE `branch_id` = '$branch_id'))
"))['sum']);
}

function getFiatProfit($connection, $branch_id, $fiat_id)
{
    return checkIfNull(mysqli_fetch_assoc($connection->query('
SELECT IFNULL(SUM(RR.sum), 0) + IFNULL(SUM(IH.sum), 0) - IFNULL(UT.sum, 0) AS `sum`
FROM users U
JOIN fiats F
LEFT JOIN (SELECT S.sum, S.user_as_owner_id, O.fiat_id FROM shares S INNER JOIN orders O ON O.order_id = S.order_id) RR 
ON RR.user_as_owner_id = U.user_id AND RR.fiat_id = F.fiat_id
LEFT JOIN (
	SELECT U.user_id, IFNULL(SUM(O.sum), 0) + IFNULL(outcome,0) AS `sum`, IFNULL(PPP.fiat_id, T.fiat_id) AS `fiat_id`
	FROM users U
	LEFT JOIN outgo O ON O.user_as_owner_id = U.user_id
	LEFT JOIN payments PPP ON PPP.method_id = O.method_id
	LEFT JOIN (
		SELECT SUM(OU.sum)/(SELECT COUNT(DISTINCT user_id) FROM users WHERE branch_id = ' . $branch_id . ' AND is_owner = 1) AS `outcome`, fiat_id
		FROM outgo OU
	    INNER JOIN methods_of_obtaining MOO ON MOO.method_id = OU.method_id
		INNER JOIN payments P ON P.method_id = MOO.method_id
		INNER JOIN users U ON U.user_id = OU.user_id
		WHERE user_as_owner_id IS NULL AND OU.branch_id IS NULL AND U.branch_id = ' . $branch_id . '
		GROUP BY P.fiat_id
	) T ON T.fiat_id = PPP.fiat_id OR PPP.fiat_id IS NULL
	WHERE U.is_owner = 1 AND U.branch_id = ' . $branch_id . '
	GROUP BY U.user_id, PPP.fiat_id
) UT ON UT.user_id = U.user_id AND UT.fiat_id = F.fiat_id
LEFT JOIN (SELECT IHH.sum, income_id, fiat_id, IHH.owner_id, IHH.user_id FROM income_history IHH INNER JOIN payments P ON P.method_id = IHH.method_id) IH ON IH.owner_id = U.user_id AND IH.fiat_id = F.fiat_id
WHERE U.is_owner = 1 AND U.branch_id = ' . $branch_id . ' AND F.fiat_id =' . $fiat_id . '
GROUP BY F.fiat_id
'))['sum']);
}


//ADDING REPORT TO DB
function addReport($connection, $unique_key, $is_weekly)
{
    return $connection->query("INSERT INTO `reports`  (`report_unique_key`, `is_weekly`) VALUES ('$unique_key', '$is_weekly') ");
}

function addVgReport($connection, $unique_key, $vg_processing_data_keys, $vg_processing_data)
{
    foreach ($vg_processing_data_keys as $key => $val) {
        $curr_data = $vg_processing_data[$key];
        $fiat_id = $vg_processing_data_keys[$key]['fiat_id'];
        $vg_data_id = $vg_processing_data_keys[$key]['vg_data_id'];
        $vg_balance = checkIfExists($curr_data[5]);
        $vg_api_balance = checkIfExists($curr_data[6]);
        $fiat_debt = checkIfExists($curr_data[8]);
        $vg_purchased = checkIfExists($curr_data[7]);
        $vg_sold = checkIfExists($curr_data[9]);
        $vg_sold_in_fiat = checkIfExists($curr_data[10]);
        $vg_callmasters_sum = checkIfExists($curr_data[11]);
        $connection->
        query("
INSERT INTO `vg_processing_reports`
(`report_unique_key`, `fiat_id`, `vg_data_id`, `vg_balance`, `vg_api_balance`, 
`fiat_debt`, `vg_purchased`, `vg_sold`, `vg_sold_in_fiat`, `vg_callmasters_sum`)
 VALUES ('$unique_key', '$fiat_id', '$vg_data_id', '$vg_balance', '$vg_api_balance',
 '$fiat_debt', '$vg_purchased', '$vg_sold', '$vg_sold_in_fiat', '$vg_callmasters_sum')");
    }
}

function addOutgoReport($connection, $unique_key, $outgo_processing_data_keys, $outgo_processing_data)
{
    foreach ($outgo_processing_data_keys as $key => $val) {
        $method_id = $outgo_processing_data_keys[$key]['method_id'];
        $outgo_type_id = $outgo_processing_data_keys[$key]['outgo_type_id'];
        $outgo_processing_report_sum = checkIfExists($outgo_processing_data[$key][4]);
        $connection->
        query("
INSERT INTO `outgoes_processing_reports`
(`report_unique_key`, `outgo_type_id`, `method_id`, `outgo_processing_report_sum`) 
VALUES ('$unique_key', '$outgo_type_id', '$method_id', '$outgo_processing_report_sum')");
    }
}

function addMethodReport($connection, $unique_key, $method_processing_data_keys, $method_processing_data)
{
    foreach ($method_processing_data_keys as $key => $val) {
        $method_id = $method_processing_data_keys[$key]['method_id'];
        $methods_processing_fiat_amount = checkIfExists($method_processing_data[$key][2]);
        $methods_processing_report_fiat_income = checkIfExists($method_processing_data[$key][5]);
        $methods_processing_report_fiat_outgo = checkIfExists($method_processing_data[$key][4]);
        $methods_processing_report_fiat_diff = checkIfExists($method_processing_data[$key][6]);
        $connection->
        query("
INSERT INTO `methods_processing_reports`
(`report_unique_key`, `method_id`, `methods_processing_fiat_amount`, 
`methods_processing_report_fiat_income`, `methods_processing_report_fiat_outgo`, `methods_processing_report_fiat_diff`)
 VALUES 
 ('$unique_key', '$method_id','$methods_processing_fiat_amount', '$methods_processing_report_fiat_income', '$methods_processing_report_fiat_outgo',
  '$methods_processing_report_fiat_diff')");
    }
}

function addDebtReport($connection, $unique_key, $debt_processing_data_keys, $debt_processing_data)
{
    foreach ($debt_processing_data_keys as $key => $val) {
        $vg_data_id = $debt_processing_data_keys[$key]['vg_data_id'];
        $fiat_id = $debt_processing_data_keys[$key]['fiat_id'];
        $debt_processing_report_sum = checkIfExists($debt_processing_data[$key][2]);
        $connection->
        query("
        INSERT INTO `debts_processing_reports`(`report_unique_key`, `vg_data_id`, `fiat_id`, `debt_processing_report_sum`) 
        VALUES ('$unique_key', '$vg_data_id', '$fiat_id','$debt_processing_report_sum' )");
    }
}

function addFiatReport($connection, $unique_key, $fiat_processing_data)
{
}

function checkIfExists($val)
{
    return $val === EMPTY_CELL ? "NULL" : $val;
}

