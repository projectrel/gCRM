<?php
function display_data($data, $options, $addition_data = NULL)
{
    //OPTIONS:::
    //RANGE - show datepicker
    //BTN-BTN-MAX show add button if allowed
    //SWITCH_VG_STAT - switch types of statistics
    //TYPE - type of tables
    session_start();
    if (!$options['prepared']) $data = mysqliToArray($data);
    return
        ("
        <div class='table-menu " . $options['type'] . "' " . ($options['range'] ? 'style = "justify-content: left;"' : '') . ">
            <h2 type=" . $options['type'] . ">" . $options['text'] . "</h2>"

            . (iCan($options['btn']) && iCanMax($options['btn-max']) ?
                "<p><a 
                    id='add-btn' 
                    href=\"#" . $options['type'] . "-Modal\" 
                    rel=\"modal:open\">" . $options['btn-text'] . "
                    </a>
                </p>" : '') .

            ($options['range'] ? "
            <div id='reportrange" . $options['range'] . "' class='reportrange'>
                <i class='fa fa-calendar'></i>&nbsp;
                <span></span> 
                <i class='fa fa-caret-down'></i>
            </div>" : '') .

            ($options['switch-vg-stat'] ?
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
        $output .= '<tr  defaultVal = "' . $var['Имя'] . '" itemId = "' . $var['id'] . '">';
        foreach ($var as $col => $val) {
            if ($col == 'id') continue;
            $actions = '';
            if ($index == 0) {
                $actions .= $options['coins'] ? '<i class="fas fa-coins" modal="#' . $options['modal'] . '"></i>' : '';
                $actions .= $options['info'] ? '<i class="fas fa-info-circle" modal="info"></i>' : '';
                $actions .= $options['minus'] ? '<i class="fas fa-minus fa-trash" modal="delete"></i>' : '';
                $actions .= ($options['edit'] && iCan($options['edit'])) ? '<i class="fas fa-edit"  modal="' . $options['type'] . '-edit"></i>' : '';
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
    session_start();
    return isset($_SESSION['id']) && isset($_SESSION['login']) && isset($_SESSION['password']);
}

function isClientAuthorized()
{
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
    switch ($name) {
        case "User":
            return userAddModal($data, $more_data);
        case "Client":
            return clientAddModal($data);
        case "Outgo":
            return outgoModal($data, $more_data);
        case "Project":
            return projectAddModal();
        case "Order":
            return orderAddModal($data, $more_data) . '' . clientAddModal($data);
        case "VG":
            return vgAddModal($more_data);
        case "Rollback":
            return rollbackModal($more_data);
        case "Debt":
            return debtModal($more_data);
        case "Owner":
            return ownerAddModal($more_data);
        case "Owner-Stats":
            return ownerAddModal($more_data) . outgoModal($data, $more_data);
        case "Branch":
            return branchAddModal($data);
        case "Fiat":
            return fiatAddModal($data);
        case "globalVG":
            return globalVgAddModal();
        case "MethodsOfObtaining":
            return methodOfObtainingModal();
        default:
            return null;
    }
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

function updateBranchMoney($connection, $branch_id, $sum, $fiat)
{
    if (count(mysqliToArray($connection->query("SELECT * FROM payments WHERE `fiat_id` = '$fiat' AND `branch_id` = '$branch_id'")))) {
        $update_branch_money = $connection->
        query("UPDATE  `payments` 
                                  SET `sum` = `sum` + '$sum'
                                  WHERE `fiat_id` = '$fiat' AND `branch_id` = '$branch_id' ");
    } else {
        $connection->query("INSERT INTO `payments` 
                                  (fiat_id, sum, branch_id) VALUES($fiat,  $sum,$branch_id )");
    }
}

function error($errorType)
{
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
    //  OR outgo_type_id = 1  |this we need coz we should take root type every time
    session_start();
    $branch_id = $_SESSION['branch_id'];
    switch (accessLevel()) {
        case 3:
            $res = mysqliToArray($connection->query("SELECT outgo_type_id, outgo_name, group_concat(DISTINCT son_id) AS sons, `active`
                FROM `outgo_types` OT
                LEFT OUTER JOIN `outgo_types_relative` OTR ON OT.outgo_type_id = OTR.parent_id
                GROUP BY outgo_type_id, outgo_name"));
            break;
        case 1:
        case 2:
        $res = mysqliToArray($connection->query("SELECT outgo_type_id, outgo_name, group_concat(DISTINCT son_id) AS sons, `active`
                FROM `outgo_types` OT
                LEFT OUTER JOIN `outgo_types_relative` OTR ON OT.outgo_type_id = OTR.parent_id
                WHERE branch_id =$branch_id OR outgo_type_id = '1'
                GROUP BY outgo_type_id, outgo_name"));
            break;
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











