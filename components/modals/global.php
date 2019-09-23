<?php
function globalVgAddModal()
{
    session_start();
    if (iCan(3))
        $output = '
<div id="globalVG-Modal" class="modal" action="" role="form">
<form id="add-globalVg-form">
  <h2 class="modal-title">Добавить валюту</h2>
  <div class="modal-inputs">
  <p>
  <input id="globalVGName" data-validation="required length" data-validation-length="min1" placeholder="Название" type="text" name="name">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Добавить">
  </form>
</div>
<div id="globalVG-edit-Modal" class="modal" action="" role="form">
<form id="edit-globalVg-form">
  <h2 class="modal-title" id="edit-globalVG-title">Редактировать валюту</h2>
  <div class="modal-inputs">
   <p>
  <input id="edit-globalVGName" data-validation="required length" data-validation-length="min1" placeholder="Название" type="text" name="name">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Сохранить">
  </form>
</div>';

    return $output;
}
