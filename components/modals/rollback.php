<?php
function rollbackModal($data)
{
    $methods = NULL;
    $clients = NULL;
    if ($data) {
        $i = 0;
        if ($data['clients']) while ($new = $data['clients']->fetch_array()) {
            $copy_of_data[$i] = $new;
            $i++;
        }
        $i = 0;
        if ($data['methods']) while ($new = $data['methods']->fetch_array()) {
            $methods[$i] = $new;
            $i++;
        }
    }
    if (!$copy_of_data) return noRollbacksModal();
    $output = '
<div id="Rollback-Modal" class="modal" action="" role="form">
<form id="pay-rollback-form">
  <h2 class="modal-title">Выплатить откат</h2>
  <div class="modal-inputs">
  <p>
<select id="clientField" data-validation="required">
  <option value="" selected disabled>Выберите клиента</option>';
    foreach ($copy_of_data as $key => $var) {
        $output .= '<option sum="' . $var['rollback_sum'] . '" fiat="' . $var['fiat_id'] . '" value="' . $var['id'] . '">' . $var['client_name'] . ' (' . $var['login'] . ')</option>';
    }
    $output .= '
</select>
</p>
  <p>
  <input id="payField" data-validation="required length" data-validation-length="min1" placeholder="Выплата" type="number" name="in" step="0.01">
  </p>
   <p>
<select id="methodField" data-validation="required">
  <option value="" selected disabled>Выберите метод оплаты</option>';
    foreach ($methods as $key => $var) {
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

function noRollbacksModal()
{
    return '<div id="Rollback-Modal" class="modal" action="">
<h2 class="no-payroll-text">Все откаты выплачены!</h2>
</div>';
}