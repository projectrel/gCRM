<?php
function vgPurchaseAddModal($data)
{
    $output = '
<div id="VGPurchase-Modal" class="modal" action="" role="form">
<form id="add-vg-purchase-form">
  <h2 class="modal-title">Закупить VG</h2>
  <div class="modal-inputs">
  <p>
  <select id="vgField" data-validation="required">
  <option value="" disabled selected>Выберите VG</option>';
    if (isset($data['vgs']))
        foreach ($data['vgs'] as $key => $var) {
            $output .= '<option value="' . $var['vg_data_id'] . '">' . $var['name'] . '</option>';
        }

    $output .= '</select>
  </p><p>
  <select id="fiatField" data-validation="required">
  <option value="" disabled selected>Выберите валюту</option>';

    if (isset($data['fiats']))
        foreach ($data['fiats'] as $key => $var) {
            $output .= '<option value="' . $var['fiat_id'] . '">' . $var['full_name'] . '</option>';
        }
    $output .= '
</select>
  </p>
  <p>
  <span>Оплата в долг</span>
  <input id="onCreditField" type="checkbox" name="credit" >
  </p>
  <p>
  <input id="vgSumField" data-validation="required length" data-validation-length="min1" placeholder="Количество" type="number" name="vg">
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

function vgPurchaseEditModal($data)
{
    $output = '
<div id="VGPurchase-edit-Modal" class="modal" action="" role="form">
<form id="edit-vg-purchase-form">
  <h2 class="modal-title" id="edit-vg-purchase-title">Редактировать закупку VG</h2>
  <div class="modal-inputs">
  <p>
  <select id="editVgField" data-validation="required">
  <option value="" disabled selected>Выберите VG</option>';
    if (isset($data['vgs']))
        foreach ($data['vgs'] as $key => $var) {
            $output .= '<option value="' . $var['vg_data_id'] . '">' . $var['name'] . '</option>';
        }

    $output .= '</select>
  </p><p>
  <select id="editFiatField" data-validation="required">
  <option value="" disabled selected>Выберите валюту</option>';

    if (isset($data['fiats']))
        foreach ($data['fiats'] as $key => $var) {
            $output .= '<option value="' . $var['fiat_id'] . '">' . $var['full_name'] . '</option>';
        }
    $output .='
</select>
  </p>
  <p>
  <span>Оплата в долг</span>
  <input id="editOnCreditField" type="checkbox" name="credit" >
  </p>
  <p>
  <input id="editVgSumField" data-validation="required length" data-validation-length="min1" placeholder="Количество" type="number" name="vg">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Сохранить">
  </form>
</div>';
    return $output;
}
