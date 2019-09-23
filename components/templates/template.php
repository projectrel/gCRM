<?php
function template($body)
{

    switch (accessLevel()) {
        case 3:
            $changeBranch = '
<div id="Change-Branch-Modal" class="modal" >
<h2 class="modal-title">Перейти в другой отдел</h2>
<form id="change-branch-form">
<div class="modal-inputs">
<p>
<select data-validation="required" id="changeBranchField">
</select>
</p>
</div>
<input class="modal-submit" type="submit" value="Перейти">
</form>
</div>
<script src="../js/changeBranch.js"></script>';
            $userIcon = 'user-shield';
            break;
        case 2:
            $changeBranch = '';
            $userIcon = 'user-cog';
            break;
        default:
            $changeBranch = '';
            $userIcon = 'user';
    }
    $add_money_btn = '<div  id="replenish-fiat-btn">Внести деньги</div>';
    $add_money_script = '<script src="../js/replenishFiat.js"></script>';
    $add_money_form = '
<div id="replenish-fiat-Modal" class="modal">
 <form id="replenish-fiat-form">
        <h2 class="modal-title" id="replenish-fiat">Внести фиат</h2>
        <div class="modal-inputs">
            <p>
            Валюта
                <select id="replenishFiatSelect" data-validation="required" >
               
                </select>
            </p>
            <p>
            Сумма
                <input type="number" id="replenishFiatSum" step="0.01" placeholder="Сумма" data-validation="required"/>
            </p>';
    session_start();
    if (($_SESSION['role'] == 'agent' || $_SESSION['role'] == 'admin') && !$_SESSION['is_owner'])
        $add_money_form .= ' <p>
            Владелец
                <select type="text" id="replenishOwnerSelect"  placeholder="Владелец" data-validation="required">
                <option selected disabled>Выберите владельца</option>
                </select>
            </p>';
    $add_money_form .= '
    </div>
            <input class="modal-submit" type="submit" value="Сохранить">
    </form>
</div>';

    $output =
        '<html>
<head>
    <title>gCRM</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../css/style.css" rel="stylesheet">
    <link href="../../css/treeTableStyle.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.14/js/jquery.tablesorter.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
<script>
        $(function(){$(".table-container").tablesorter();});
    </script>
</head>
<body>
<div class="custom-alert">
<div class="alert-text-box"></div>
<div class="close-btn-box"> <i class="fa fa-chevron-right"></i></div>
</div>
<div class="main-header">
<div id="menu-burger" class="menu-burget-btn">
<i class="fas fa-bars fa-2x"></i>
</div>
<div class="account-name-menu-btn-box">
<i class="fas fa-building fa-2x"></i>
<p class="menu-n" id="branch-t">' . $_SESSION['branch'] . '</p>
</div>
<div class="account-name-menu-btn-box">
<i class="fas fa-coins fa-2x"></i>
</div>
<div id="logout" class="account-name-menu-btn-box">
<i class="fas fa-' . $userIcon . ' fa-2x"></i>
<p class="menu-n">' . $_SESSION['name'] . '</p>
<a  href="../api/auth/logout.php"><i class="fas fa-sign-out-alt fa-2x"></i></a>
</div>
</div>
<div id="Branch-money-info-modal" class="modal">
<div class="fiats"></div>
' . $add_money_btn . '
</div>
<div class="main-wrapper">';
    ob_start();
    include 'menu.php';
    $output .= ob_get_clean();
    $output .= '<div class="loader"><div class="spinner"></div></div><div id="wrapper">' . ($body ? $body : '<h1>NO INFO ABOUT CURRENT PAGE</h1>') . '</div>
</div>
' . $add_money_form . '
' . $changeBranch . '
<script src="../js/treeTable.js"></script>
<script src="../js/add-handlers.js"></script>
<script src="../js/edit-handlers.js"></script>
<script src="../js/edit.js"></script>
<script src="../js/listeners.js"></script>
<script src="../js/statistics.js"></script>
' . $add_money_script . '
</body>
</html>';
    return $output;
}