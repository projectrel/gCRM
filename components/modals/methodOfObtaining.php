<?php
function methodOfObtainingModal()
{
    return '
<div id="MethodsOfObtaining-Modal" class="modal" action="" role="form">
<form id="add-method-of-obtaining-form">

  <h2 class="modal-title">Добавить метод оплаты</h2>
  <div class="modal-inputs">
  <p>
  <input id="method-name" data-validation="required"  placeholder="Название" type="text" name="name" >
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Добавить">
  </form>
</div>' . methodOfObtainingEditModal();
}

function methodOfObtainingEditModal()
{
    return '
<div id="MethodsOfObtaining-edit-Modal" class="modal" action="" role="form">
<form id="method-of-obtaining-edit-form">
  <h2 class="modal-title">Редактировать метод оплаты</h2>
  <div class="modal-inputs">
  <p>
  <input id="method-edit-name" data-validation="required"  placeholder="Название" type="text" name="name" >
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Сохранить">
  </form>
</div>';
}