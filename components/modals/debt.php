<?php
function debtModal($data)
{
    $methods = NULL;
    $clients = NULL;
    if ($data) {
        $i = 0;
        if($data['clients'])while ($new = $data['clients']->fetch_array()) {
            $copy_of_data[$i] = $new;
            $i++;
        }
        $i = 0;
        if($data['methods'])while ($new = $data['methods']->fetch_array()) {
            $methods[$i] = $new;
            $i++;
        }
    }
    if (!$copy_of_data) return '<div id="Debt-Modal" class="modal" action="">
<h2 class="no-payroll-text">Все долги погашены!</h2>
</div>';
    $output = '
<div id="Debt-Modal" class="modal" action="" role="form">
<form id="payback-debt-form">
  <h2 class="modal-title">Погасить долг</h2>
  <div class="modal-inputs">
  <p>
<select id="debtorField" data-validation="required length" data-validation-length="min1">
  <option value="" selected disabled>Выберите должника</option>';
    foreach ($copy_of_data as $key => $var) {
        $output .= '<option sum="'.$var['debt'].'" fiat="'.$var['fiat_id'].'" value="' . $var['id'] . '">' . $var['client_name'] . ' (' . $var['login'] . ')</option>';
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
    if($methods)
    foreach ($methods as $key => $var) {
        $output .= '<option value="' . $var['method_id'] . '">' . $var['method_name'] .'</option>';
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