<?php
function fiatAddModal($data)
{
    $output = '
<div id="Fiat-Modal" class="modal" action="" role="form">
<form id="add-fiat-form">
  <h2 class="modal-title">Добавить валюту</h2>
  <div class="modal-inputs">

  <p >
  <input id="nameFiatField" data-validation="required length" data-validation-length="min1" placeholder="Сокращение" type="text" name="name">
  </p>
  <p >
  <input id="fullNameFiatField" data-validation="required length" data-validation-length="min1" placeholder="Название" type="text" name="name">
  </p>
    <p>
  <input id="codeField" data-validation="required" placeholder="Код" type="number"  name="key">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Добавить">
  </form>
</div>';
    $output .= fiatEditModal();
    return $output;
}

function fiatEditModal()
{
return '
<div id="Fiat-edit-Modal" class="modal" action="" role="form">
<form id="edit-fiat-form">
  <h2 class="modal-title" id="edit-fiat-title">Редактировать валюту</h2>
  <div class="modal-inputs">
  
  <p >
  Сокращение
  <input id="editNameFiatField" data-validation="required length" data-validation-length="min1" placeholder="Сокращение" type="text" name="name">
  </p>
  <p >
  Название
  <input id="editFullNameFiatField" data-validation="required length" data-validation-length="min1" placeholder="Название" type="text" name="name">
  </p>
    <p>
    Код
  <input id="editCodeField" data-validation="required" placeholder="Код" type="number" name="key">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Сохранить">
  </form>
</div>';
}