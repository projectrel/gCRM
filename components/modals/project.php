<?php
function projectAddModal()
{
    $output = '
<div id="Project-Modal" class="modal" action="" role="form">
<form id="add-project-form">
  <h2 class="modal-title">Добавить проект</h2>
  <div class="modal-inputs">
   <p>
  Название
  <input id="addNameProjectField" data-validation="required length" data-validation-length="min1" placeholder="Название" type="text" name="name">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Добавить">
  </form>
</div>';
    $output .= projectEditModal();
    return $output;
}

function projectEditModal()
{
return '
<div id="Project-edit-Modal" class="modal" action="" role="form">
<form id="edit-project-form">
  <h2 class="modal-title" id="edit-project-title">Редактировать проект</h2>
  <div class="modal-inputs">
  <p >
  Название
  <input id="editNameProjectField" data-validation="required length" data-validation-length="min1" placeholder="Название" type="text" name="name">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Сохранить">
  </form>
</div>';
}