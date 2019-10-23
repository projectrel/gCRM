<?php
function debtModal($data)
{

    foreach ($data as $key => $var) {
        $data[$key] = mysqliToArray($var);
    }
    if (!$data) return '<div id="Debt-Modal" class="modal" action="">
<h2 class="no-payroll-text">Все долги погашены!</h2>
</div>';
    if (!$data['methods']) return '<div id="Debt-Modal" class="modal" action="">
<h2 class="no-payroll-text">Не существует методов оплат!</h2>
</div>';
    $output = '
<div id="Debt-Modal" class="modal" action="" role="form">
<form id="payback-debt-form">
  <h2 class="modal-title">Погасить долг</h2>
  <div class="modal-inputs">
  <p>
<select id="debtorField" data-validation="required length" data-validation-length="min1">
  <option value="" selected disabled>Выберите должника</option>';
    foreach ($data['clients'] as $key => $var) {
        $output .= '<option sum="' . $var['debt'] . '" fiat="' . $var['fiat_id'] . '" value="' . $var['id'] . '">' . $var['client_name'] . ' (' . $var['login'] . ')</option>';
    }
    $output .= '
</select>
</p>
  <p>
  <input id="paybackField" data-validation="required length" data-validation-length="min1" placeholder="Выплата" type="number" name="in" step="0.01">
  </p>
  <p>
<select id="methodField" data-validation="required">
  <option value="" selected disabled>Выберите метод оплаты</option>';

    foreach ($data["methods"] as $key => $var) {
        $output .= '<option value="' . $var['method_id'] . '">' . $var['method_name'] . '</option>';
    }
    $output .= '
</select>
</p>
  </div>
  <input class="modal-submit" type="submit" value="Выплатить">
  </form>
</div>';
    if(!isset($_SESSION))
    session_start();
    if (iCan(2)) {
        $output .= debtEditModal($data);
    }

    return $output;
}

function debtEditModal($data)
{
    $output = '
<div id="Debt-edit-Modal" class="modal" action="" role="form">
<form id="edit-payback-debt-form">
  <h2 class="modal-title">Погасить долг</h2>
  <div class="modal-inputs">
  <p>
<select id="editDebtorField" data-validation="required length" data-validation-length="min1">
  <option value="" selected disabled>Выберите должника</option>';
    foreach ($data['clients'] as $key => $var) {
        $output .= '<option sum="' . $var['debt'] . '" fiat="' . $var['fiat_id'] . '" value="' . $var['id'] . '">' . $var['client_name'] . ' (' . $var['login'] . ')</option>';
    }
    $output .= '
</select>
</p>
  <p>
  <input id="editPaybackField" data-validation="required length" data-validation-length="min1" placeholder="Выплата" type="number" name="sum" step="0.01">
  </p>
  <p>
<select id="editMethodField" data-validation="required">
  <option value="" selected disabled>Выберите метод оплаты</option>';
    foreach ($data["methods"] as $key => $var) {
        $output .= '<option value="' . $var['method_id'] . '">' . $var['method_name'] . '</option>';
    }
    $output .= '
</select>
</p>
  </div>
  <input class="modal-submit" type="submit" value="Выплатить">
  </form>
</div>';

    return $output;
}