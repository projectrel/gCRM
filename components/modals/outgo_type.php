<?php
function outgoTypeModal()
{
    $output = '
<div id="outgo-type-add-Modal" class="modal" action="" role="form">
<form id="add-outgo-type-form">
  <h2 class="modal-title">Добавить тип расходов в категорию <span id="outgo-type-add-category"></span></h2>
  <div class="modal-inputs">
  <p id="name">
  <input id="name-add" data-validation="required length" data-validation-length="min1" placeholder="Название" type="text" name="name">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Сохранить">
  </form>
</div>';

    $output .= '
<div id="outgo-type-edit-Modal" class="modal" action="" role="form">
<form id="edit-outgo-type-form">
  <h2 class="modal-title">Редактировать тип расходов <span id="outgo-type-edit-category"></span></h2>
  <div class="modal-inputs">
  <p id="name-edt">
  <input id="name-edit" data-validation="required length" data-validation-length="min1" placeholder="Название" type="text" name="name">
  </p>
    </div>
  <input class="modal-submit" type="submit" value="Сохранить">
  </form>
</div>';
    return $output;
}

