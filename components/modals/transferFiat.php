<?php

function transferFiatModal($data)
{
    if (!$data) return '<div id="Order-Modal" class="modal" action="" role="form">
<h2 class="no-payroll-text">Сначало добавьте методы оплаты</h2>
</div>';
    $output = '
<div id="Transfer-Modal" class="modal" role="form">
<form id="transfer-fiat-form">
  <h2 class="modal-title">Перевести деньги</h2>
  <div class="modal-inputs">
  <p>
  Счет(ресурс)
<select id="fromMethodField" data-validation="required">
  <option value="" disabled selected>Выберите счет</option>';
    foreach ($data as $key => $var) {
        $output .= '<option value="' . $var["id"] . '">' . $var["name"] . '</option>';
    }
    $output .= '
  </select>
</p>
<p>
  Сумма вывода
  <input id="sumFromField" min=0 data-validation="required length" data-validation-length="min1" placeholder="Кол-во валюты" type="number" step="0.01" name="sum1">
  </p>
<p>
Счет(цель)
<select id="ToMethodField" data-validation="required">
<option value="" disabled selected>Выберите счет</option>';
    foreach ($data as $key => $var) {
        $output .= '<option value="' . $var["id"] . '">' . $var["name"] . '</option>';
    }
    $output .= '
</select>
</p>
<p>
  Сумма прихода
  <input id="sumToField" min=0 data-validation="required length" data-validation-length="min1" placeholder="Кол-вовалюты" type="number" step="0.01" name="sum2">
  </p>

  </div>
  <input class="modal-submit" type="submit" value="Оформить">
  </form>
</div>';
    return $output;
}
