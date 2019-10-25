<?php
function display_data($data, $options, $addition_data = NULL)
{
    //OPTIONS:::
    //RANGE - show datepicker
    //BTN-BTN-MAX show add button if allowed
    //SWITCH_VG_STAT - switch types of statistics
    //TYPE - type of tables
    if (!isset($_SESSION))
        session_start();
    if (!array_key_exists("prepared", $options)) $data = mysqliToArray($data);
    return
        ("
        <div class='table-menu " . $options['type'] . "' " . (array_key_exists("range", $options) ? 'style = "justify-content: left;"' : '') . ">
            <h2 type=" . $options['type'] . ">" . $options['text'] . "</h2>"

            . (iCan(array_key_exists("btn", $options) ? $options['btn'] : NULL) && iCanMax(array_key_exists("btn-max", $options) ? $options['btn-max'] : NULL) ?
                "<p><a 
                    id='add-btn' 
                    href=\"#" . $options['type'] . "-Modal\" 
                    rel=\"modal:open\">" . $options['btn-text'] . "
                    </a>
                </p>" : '') .

            (array_key_exists("range", $options) ? "
            <div id='reportrange" . $options['range'] . "' class='reportrange'>
                <i class='fa fa-calendar'></i>&nbsp;
                <span></span> 
                <i class='fa fa-caret-down'></i>
            </div>" : '') .

            (array_key_exists("switch-vg-stat", $options) ?
                "<button class='switch-vg-stat' id='add-btn'>
                 </button>" : '') . "
        </div>
        <div class='table-wrapper " . $options['type'] . "' id='table-wrapper'>
            <a 
                    class='display-none' 
                    href='#" . $options['type'] . "-edit-Modal' 
                    rel=\"modal:open\">
            </a>
            " . makeTable($data, $options) . "
        </div>
        " . chooseAddModal($options['type'], $data, $addition_data)
        );
}

function makeTable($data, $options)
{
    if (!$data || count($data) === 0) {
        return '<h2>Пусто</h2>';
    }
    $output = "<table class='table-container table table-fixed'>";

    foreach ($data as $key => $var) {
        $index = 0;
        if ($key === 0) {
            $output .= '<thead id="table-head"><tr>';
            foreach ($var as $col => $val) {
                if ($col == 'id') continue;
                $output .= "<th><div class='col-wrap'><p>" . $col . "</p><span></span></div><input id=$index-i></th>";
                $index++;
            }
            $output .= '</tr></thead><tbody id="tbody">';
        }
        $index = 0;
        $output .= '<tr  defaultVal = "' . (isset($var['Имя']) ? $var['Имя'] : "") . '" itemId = "' . $var['id'] . '">';
        foreach ($var as $col => $val) {
            if ($col == 'id') continue;
            $actions = '';
            if ($index == 0) {
                $actions .= isset($options['coins']) ? '<i class="fas fa-coins" modal="#' . $options['modal'] . '"></i>' : '';
                $actions .= isset($options['info']) ? '<i class="fas fa-info-circle" modal="info"></i>' : '';
                $actions .= isset($options['minus']) ? '<i class="fas fa-minus fa-trash" modal="delete"></i>' : '';
                $actions .= (isset($options['edit']) && iCan(isset($options['edit']))) ? '<i class="fas fa-edit"  modal="' . $options['type'] . '-edit"></i>' : '';
            }
            if ($col == 'статус') {
                $output .= '<td class=' . $index . '-f title="' . $val . '"><p style="display: none">' . $val . '</p>
                <div class="button b2" id="button-10">
                <input type="checkbox" class="checkbox status ' . ($val == 0 ? 'checked' : '') . '" ' . ($val == 0 ? 'checked' : '') . '>
                <div class="knobs"></div>
                </div>
            </td>';
            } else if ($col == 'участие в балансе') {
                $output .= '<td class=' . $index . '-f title="' . $val . '"><p style="display: none">' . $val . '</p>
                <div class="button b2" id="button-10">
                <input type="checkbox" class="checkbox participates ' . ($val == 0 ? 'checked' : '') . '" ' . ($val == 0 ? 'checked' : '') . '>
                <div class="knobs"></div>
                </div>
            </td>';
            } else {
                $output .= '<td class=' . $index . '-f title="' . $val . '">' . $actions . formatData($val) . '</td>';
            }
            $index++;
        }
        $output .= '</tr>';
    }
    $output .= '</tbody></table>';
    return $output;
}

function formatData($val)
{
    if (is_numeric($val))
        $val = round($val, 2);
    return $val === '' || $val === null ? '-' : $val;
}

function clean($value = "")
{
    $value = trim($value);
    $value = strip_tags($value);
    return $value;
}

function isAuthorized()
{
    if (!isset($_SESSION))
        session_start();
    return isset($_SESSION['id']) && isset($_SESSION['login']) && isset($_SESSION['password']);
}

function isClientAuthorized()
{
    if (!isset($_SESSION))
        session_start();
    return isset($_SESSION['client_id']) && isset($_SESSION['client_login']) && isset($_SESSION['client_password']);
}

function mysqliToArray($mysqli_result)
{
    if (!$mysqli_result)
        return false;
    $i = 0;
    $data = null;
    while ($new = mysqli_fetch_assoc($mysqli_result)) $data[$i++] = $new;
    return $data;
}


function chooseAddModal($name, $data, $more_data = NULL)
{
    foreach (glob($_SERVER['DOCUMENT_ROOT'] . "/components/modals/*.php") as $filename) {
        include_once $filename;
    }
    $output = "";
    switch ($name) {
        case "User":
            $output = userAddModal($data, $more_data);
            break;
        case "Client":
            $output = clientAddModal($data);
            break;
        case "Outgo":
            $output = outgoModal($data, $more_data);
            break;
        case "Project":
            $output = projectAddModal();
            break;
        case "Order":
            $output = orderAddModal($data, $more_data) . '' . clientAddModal($data);
            break;
        case "VG":
            $output = vgAddModal($more_data);
            break;
        case "Rollback":
            $output = rollbackModal($more_data);
            break;
        case "Debt":
            $output = debtModal($more_data);
            break;
        case "Owner":
            $output = ownerAddModal($more_data);
            break;
        case "Owner-Stats":
            $output = ownerAddModal($more_data) . outgoModal($data, $more_data);
            break;
        case "Branch":
            $output = branchAddModal($data);
            break;
        case "Fiat":
            $output = fiatAddModal($data);
            break;
        case "VGPaybackDebt":
            $output = vgDebtPaybackEditModal($more_data);
            break;
        case "VGPurchase":
            $output = vgPurchaseAddModal($more_data);
            break;
        case "VGDebt":
            $output = vgPaybackDebtModal($more_data);
            break;
        case "globalVG":
            $output = globalVgAddModal();
            break;
        case "MethodsOfObtaining":
            $output = methodOfObtainingModal($more_data);
            break;
        default:
            $output = "";
    }
    $output .= transferFiatModal(get_all_methods_of_branch());
    return $output;
}

function accessLevel($role = false)
{
    $r = $role ? $role : $_SESSION['role'];
    switch ($r) {
        case 'agent':
            return 1;
        case 'admin':
            return 2;
        case 'moder':
            return 3;
    }
}

function iCan($actionLvl)
{
    return !is_null($actionLvl) && $actionLvl <= accessLevel();
}

function iCanMax($actionLvl)
{
    return is_null($actionLvl) || $actionLvl >= accessLevel();
}

function heCan($role, $actionLvl)
{
    return !is_null($actionLvl) && $actionLvl <= accessLevel($role);
}

function generateRandomString($length = 40)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function updateMethodMoney($connection, $method_id, $sum)
{
    $update_method_money = $connection->
    query("UPDATE  `payments` SET `sum` = `sum` + '$sum'
           WHERE `method_id` = '$method_id' ");
    return $update_method_money;
}

function getFiatIdByMethod($connection, $method_id)
{
    $method_payments_info = mysqli_fetch_assoc($connection->
    query("SELECT * FROM `payments` WHERE `method_id` = '$method_id' "));
    return $method_payments_info['fiat_id'];
}

function error($errorType, $info = NULL)
{
    if ($info)
        echo json_encode(array("success" => false, "error" => $errorType, "info" => $info));
    else
        echo json_encode(array("success" => false, "error" => $errorType));

    return false;
}

function display_tree_table()
{
    include_once $_SERVER['DOCUMENT_ROOT'] . "/components/modals/outgo_type.php";

    $output = '
<div id="jquery-accordion-menu" class="jquery-accordion-menu white">
    <div id="types-wrapper">
    <div id="types-wrapper-header">
        <h2>Типы расходов</h2>
         <div id="reportrange-types" class="reportrange">
                <i class="fa fa-calendar"></i>&nbsp;
                <span></span> 
                <i class="fa fa-caret-down"></i>
            </div>
            </div>
        <table>
            <thead>
                <i class="fas fa-plus fa-2x" id="global-add-type"></i>
                <th>Название</th>
                <th>Статус</th>
            </thead>
        </table>
        <ul id="types-list"></ul>
    </div>
</div>';
    return $output . outgoTypeModal();
}

function getOutGoTypes($connection)
{
    include_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";
    $root_type = ROOT_TYPE;
    $vg_purchase_type = VG_PURCHASE_TYPE;
    if (!isset($_SESSION))
        session_start();
    $branch_id = $_SESSION['branch_id'];
    switch (accessLevel()) {
        case 3:
            $res = mysqliToArray($connection->query("SELECT OT.outgo_type_id, outgo_name, group_concat(DISTINCT son_id) AS sons, `active`, IFNULL(MAX(LC.change_date),'-') AS 'пос. редакт.'
                FROM `outgo_types` OT
                LEFT OUTER JOIN `outgo_types_relative` OTR ON OT.outgo_type_id = OTR.parent_id
                LEFT OUTER JOIN changes LC ON OT.outgo_type_id = LC.outgo_type_id
                GROUP BY OT.outgo_type_id, outgo_name"));
            break;
        case 1:
            $res = mysqliToArray($connection->query("SELECT OT.outgo_type_id, outgo_name, group_concat(DISTINCT son_id) AS sons, `active`,
                IFNULL(MAX(LC.change_date),'-') AS 'пос. редакт.'
                FROM `outgo_types` OT
                LEFT OUTER JOIN `outgo_types_relative` OTR ON OT.outgo_type_id = OTR.parent_id
                LEFT OUTER JOIN changes LC ON OT.outgo_type_id = LC.outgo_type_id
                WHERE OT.branch_id =$branch_id OR OT.outgo_type_id = '$root_type' OR OT.outgo_type_id = '$vg_purchase_type'
                GROUP BY OT.outgo_type_id, outgo_name"));
            break;
        case 2:
            $res = mysqliToArray($connection->query("SELECT outgo_type_id, outgo_name, group_concat(DISTINCT son_id) AS sons, `active`
                FROM `outgo_types` OT
                LEFT OUTER JOIN `outgo_types_relative` OTR ON OT.outgo_type_id = OTR.parent_id
                WHERE branch_id =$branch_id OR outgo_type_id = '$root_type' OR outgo_type_id = '$vg_purchase_type'
                GROUP BY outgo_type_id, outgo_name"));
            break;
        default:
            $res = NULL;
    }


    return $res;
}

function getTypeByTypes($types, $type_id)
{
    foreach ($types as $key => $var) {
        if ($var['outgo_type_id'] == $type_id) {
            $cur = $var;
        }
    }
    return $cur;
}

function tree($node, $types)
{
    if (is_null($node) || !is_array($node) || is_null($node['sons']) || $node['sons'] == '' || $node['sons'] == '{NULL}' ||
        $node['sons'] == NULL || $node['sons'] == 'NULL') {
        return array("node" => $node);
    }
    $sons = explode(',', $node['sons']);
    $layer = array();
    for ($i = 0; $i < count($sons); $i++) {
        $ggg = null;

        foreach ($types as $key => $var) {
            if ($var['outgo_type_id'] == $sons[$i]) {
                $ggg = $var;
            }
        }
        array_push($layer, $ggg);
    }
    $next = array();
    foreach ($layer as $key => $var) {
        array_push($next, tree($var, $types));
    }
    return array("node" => $node, "children" => $next);
}

function children_list($node, $types)
{
    if (is_null($node) || !is_array($node) || is_null($node['sons']) || $node['sons'] == '' || $node['sons'] == '{NULL}' ||
        $node['sons'] == NULL || $node['sons'] == 'NULL') {
        return array($node);
    }
    $sons = explode(',', $node['sons']);
    $layer = array();
    for ($i = 0; $i < count($sons); $i++) {
        $ggg = null;

        foreach ($types as $key => $var) {
            if ($var['outgo_type_id'] == $sons[$i]) {
                $ggg = $var;
            }
        }
        array_push($layer, $ggg);
    }
    $next = array();
    foreach ($layer as $key => $var) {
        $newArr = children_list($var, $types);
        foreach ($newArr as $item) {
            array_push($next, $item);
        }
    }
    array_push($next, $node);
    return $next;
}

function save_change_info($connection, $type, $id)
{
    if (!isset($_SESSION))
        session_start();
    $user_id = $_SESSION['id'];
    $field = $type . "_id";
    $query = "INSERT INTO `changes` (`$field`, `change_user_id`) VALUES ('$id', '$user_id')";
    return $connection->query($query);
}

function get_all_methods_of_branch()
{
    if (!isset($_SESSION))
        session_start();
    $branch_id = $_SESSION['branch_id'];
    include_once $_SERVER['DOCUMENT_ROOT'] . "/db.php";
    return mysqliToArray($GLOBALS['connection']->query("
SELECT MOO.method_id AS `id`, concat(MOO.method_name,'(',P.sum, ' ', F.full_name,')') AS `name` FROM `methods_of_obtaining` MOO 
INNER JOIN payments P ON MOO.method_id = P.method_id
INNER JOIN fiats F ON P.fiat_id = F.fiat_id
WHERE MOO.branch_id = '$branch_id' AND MOO.is_active = 1"));
}










