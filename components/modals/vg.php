<?php
function vgAddModal($data)
{
    $output = '
<div id="VG-Modal" class="modal" action="" role="form">
<form id="add-vg-form">
  <h2 class="modal-title">Добавить валюту</h2>
  <div class="modal-inputs">
  <p>
  <select id="nameVgnField" data-validation="required">
  <option value="" disabled selected>Выберите VG</option>';
    if (isset($data['vgs'])) foreach ($data['vgs'] as $key => $var) {
        $output .= '<option value="' . $var['vg_id'] . '">' . $var['name'] . '</option>';
    }
    $output .= '
</select>
  </p>
  <p id="nameVgn">
  <input id="nameField" data-validation="required length" data-validation-length="min1" placeholder="Название" type="text" name="name">
  </p>
  <p>
  <input id="inField" min=0 data-validation="required length" data-validation-length="min1" placeholder="покупка %" type="number" name="in" step=0.01>
  </p>
  <p>
  <input id="outField" min=0 data-validation="required length" data-validation-length="min1" placeholder="продажа %" type="number" name="out" step=0.01>
  </p>
   <p>
  <input id="urlField" placeholder="url" type="url" name="url">
  </p>
    <p>
  <input id="keyField" placeholder="key"  name="key">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Добавить">
  </form>
</div>';
    session_start();
    if (iCan(2))
        $output .= vgEditModal();

    return $output;
}

function vgEditModal()
{
    return '
<div id="VG-edit-Modal" class="modal" action="" role="form">
<form id="edit-vg-form">
  <h2 class="modal-title" id="edit-vg-title">Редактировать валюту</h2>
  <div class="modal-inputs">
  <p>
  <input id="editNameField" data-validation="required length" data-validation-length="min1" placeholder="Название" type="text" name="name">
  </p>
  <p>
  <input id="editInField" min=0 data-validation="required length" data-validation-length="min1" placeholder="покупка %" type="number" name="in" step=0.01>
  </p>
  <p>
  <input id="editOutField" min=0 data-validation="required length" data-validation-length="min1" placeholder="продажа %" type="number" name="out" step=0.01>
  </p>
   <p>
  <input id="editUrlField" placeholder="url" type="url" name="url">
  </p>
  <p>
  <input id="editKeyField" placeholder="key" name="key">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Сохранить">
  </form>
</div>';
}
