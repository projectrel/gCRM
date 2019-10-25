<?php
function methodOfObtainingModal($data)
{
    $output = '
<div id="MethodsOfObtaining-Modal" class="modal" action="" role="form">
<form id="add-method-of-obtaining-form">

  <h2 class="modal-title">Добавить счет</h2>
  <div class="modal-inputs">
  <p>
  <input id="method-name" data-validation="required"  placeholder="Название" type="text" name="name" >
  </p>';
   $output .= '<p>
  <select id="methodFiatField" data-validation="required">
  <option value="" disabled selected>Выберите валюту</option>';
    if (isset($data['fiats']))
        foreach ($data['fiats'] as $key => $var) {
            $output .= '<option value="' . $var['fiat_id'] . '">' . $var['full_name'] . '</option>';
        }


    $output .= '
</select>
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Добавить">
  </form>
</div>' . methodOfObtainingEditModal($data);
    return $output;
}

function methodOfObtainingEditModal($data)
{
    $output = '
<div id="MethodsOfObtaining-edit-Modal" class="modal" action="" role="form">
<form id="method-of-obtaining-edit-form">
  <h2 class="modal-title">Редактировать счет</h2>
  <div class="modal-inputs">
  <p>
  <input id="method-edit-name" data-validation="required"  placeholder="Название" type="text" name="name" >
  </p>';
   $output .= '<p>
  <select id="editMethodFiatField" data-validation="required">
  <option value="" disabled selected>Выберите валюту</option>';
    if (isset($data['fiats']))
        foreach ($data['fiats'] as $key => $var) {
            $output .= '<option value="' . $var['fiat_id'] . '">' . $var['full_name'] . '</option>';
        }
    $output .= '
</select>
</p>
  </div>
  <input class="modal-submit" type="submit" value="Сохранить">
  </form>
</div>';
return $output;
}