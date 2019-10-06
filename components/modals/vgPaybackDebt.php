<?php
function vgPaybackDebtModal($data)
{
    $output = '
<div id="VGDebt-Modal" class="modal" action="" role="form">
<form id="payback-vg-debt-form">
  <h2 class="modal-title">Выплатить задолженность по VG</h2>
  <div class="modal-inputs">
  <p>
  <select id="vgDebtField" data-validation="required">
  <option value="" disabled selected>Выберите VG</option>';
    if (isset($data['vgs']))
        foreach ($data['vgs'] as $key => $var) {
            $output .= '<option value="' . $var['vg_data_id'] . '">' . $var['name'] . '</option>';
        }

    $output .= '</select>
  </p><p>
  <select id="methodDebtField" data-validation="required">
  <option value="" disabled selected>Выберите метод оплаты</option>';

    if (isset($data['methods']))
        foreach ($data['methods'] as $key => $var) {
            $output .= '<option value="' . $var['method_id'] . '">' . $var['method_name'] . '</option>';
        }
    $output .= '
</select>
  </p>
  <p>
  <input id="vgSumDebtField" data-validation="required length" data-validation-length="min1" placeholder="Количество" type="number" name="vg">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Погасить">
  </form>
</div>';
    return $output;
}

function vgDebtPaybackEditModal($data)
{
    $output = '
<div id="VGPaybackDebt-edit-Modal" class="modal" action="" role="form">
<form id="edit-payback-vg-purchase-debt-form">
  <h2 class="modal-title" id="edit-vg-purchase-debt-title">Редактировать выплату задолженности по VG</h2>
  <div class="modal-inputs">
  <p>
  <select id="editVgDebtField" data-validation="required">
  <option value="" disabled selected>Выберите VG</option>';
    if (isset($data['vgs']))
        foreach ($data['vgs'] as $key => $var) {
            $output .= '<option value="' . $var['vg_data_id'] . '">' . $var['name'] . '</option>';
        }

    $output .= '</select>
  </p><p>
  <select id="editFiaDebttField" data-validation="required">
  <option value="" disabled selected>Выберите валюту</option>';

    if (isset($data['fiats']))
        foreach ($data['fiats'] as $key => $var) {
            $output .= '<option value="' . $var['fiat_id'] . '">' . $var['full_name'] . '</option>';
        }
    $output .='
</select>
  </p>
  <p>
  <input id="editVgSumDebtField" data-validation="required length" data-validation-length="min1" placeholder="Количество" type="number" name="vg">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Сохранить">
  </form>
</div>';
    return $output;
}
