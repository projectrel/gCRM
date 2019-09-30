<?php
function vgDebtAddModal($data)
{
    $output = '
<div id="VGDebt-Modal" class="modal" action="" role="form">
<form id="add-vg-debt-form">
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
  <select id="fiaDebttField" data-validation="required">
  <option value="" disabled selected>Выберите валюту</option>';

    if (isset($data['fiats']))
        foreach ($data['fiats'] as $key => $var) {
            $output .= '<option value="' . $var['fiat_id'] . '">' . $var['full_name'] . '</option>';
        }
    $output .= '
</select>
  </p>
  <p>
  <p>
  <input id="vgSumDebtField" data-validation="required length" data-validation-length="min1" placeholder="Количество" type="number" name="vg">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Добавить">
  </form>
</div>';
    session_start();
    if (iCan(1))
        $output .= vgPurchaseEditModal($data);

    return $output;
}

function vgDebtEditModal($data)
{
    $output = '
<div id="VGDebt-edit-Modal" class="modal" action="" role="form">
<form id="edit-vg-debt-form">
  <h2 class="modal-title" id="edit-vg-purchase-title">Редактировать выплату задолженности по VG</h2>
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
  <p>
  <input id="editVgSumDebtField" data-validation="required length" data-validation-length="min1" placeholder="Количество" type="number" name="vg">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Сохранить">
  </form>
</div>';
    return $output;
}
