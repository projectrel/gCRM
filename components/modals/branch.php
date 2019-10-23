<?php

function branchAddModal($data)
{
    $output = '
<div id="Branch-Modal" class="modal" action="" role="form">
    <form id="add-branch-form">
        <h2 class="modal-title">Добавить предприятие</h2>
        <div class="modal-inputs">
            <p>
            Название
                <input id="nameField" data-validation="required length" data-validation-length="min1" placeholder="Название" type="text" name="name">
            </p>
               <p>
            Interkassa id
                <input id="ikId"   placeholder="Interkassa id" type="text" name="ik_id">
            </p>
            </div>
            <input class="modal-submit" type="submit" value="Добавить">
            
    </form>
</div>
';
    if(!isset($_SESSION))
    session_start();
    if (iCan(2))
        $output .= branchEditModal();
    return $output;
}


function branchEditModal()
{
    return '
<div id="Branch-edit-Modal" class="modal" action="" role="form">
    <form id="edit-branch-form">
        <h2 class="modal-title" id="edit-branch-title">Редактирова данные предприятия</h2>
        <div class="modal-inputs">
            <p>
            Название
                <input id="editNameField" data-validation="required length" data-validation-length="min1" placeholder="Название" type="text" name="name">
            </p>
              <p>
            Interkassa id
                <input id="editIkId"   placeholder="Interkassa id" type="text" name="ik_id">
            </p>
            </div>
            <input class="modal-submit" type="submit" value="Сохранить">
    </form>
</div>';
}