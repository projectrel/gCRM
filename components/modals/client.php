<?php
function clientAddModal($data)
{
    $output = '
<div id="Client-Modal" class="modal" action="" role="form">
<form id="add-client-form">
  <h2 class="modal-title">Добавить клиента</h2>
  <div class="modal-inputs">
  <p style="grid-column-start: 1;grid-column-end: 3;">
  Имя
  <input id="firstNameField" data-validation="required length" data-validation-length="min3" placeholder="Имя" type="text" name="name">
  </p>
  <p>
  Фамилия
  <input id="lastNameField" placeholder="Фамилия" type="text" name="lastName">
  </p>
  <p>
    ID, цифровой логин
  <input id="bynameField"  placeholder="Логин" type="text" name="">
    <button type="button" class="genid">Ген</button>
  </p>
  <p>
  Пароль клиента
  <input id="passwordField" placeholder="Пароль" type="text" name="">
    <button type="button" class="genpass">Ген</button>
  </p>
  <p style="padding-top: 15px">
  <span>Страничка оплаты</span>
    <input id="pay_page" type="checkbox" name="lastName">
   <span> Оплата в долг</span>
  <input id="pay_in_debt"  type="checkbox" name="lastName">
  <span style="display:none;">Оплата платежкой</span>
  <input style="display:none;" id="payment_system" type="checkbox" name="lastName">

</p>
   <p>
   Номер телефона
  <input id="phoneField" placeholder="Телефон" type="text" name="phone">
  </p>
     <p>
  Макс долг (деньги)
  <input id="maxDebtField" placeholder="Макс долг" type="number" name="lastName">
  </p>
  <p>
   Телеграм
  <input id="tgField" placeholder="Телеграм" type="text" name="tg">
  </p>
  <p>
  Email
  <input id="emailField"   placeholder="Email" type="email" name="email">
  </p>

  <p>
  <textarea id="descriptionField" rows="3"  placeholder="Описание" type="text" name="description"></textarea>
  </p>
 
  </div>
  <input class="modal-submit" type="submit" value="Добавить">
  </form>
</div>';
    $output .= clientEditModal();
    $output .= clientInfoModal();
    return $output;
}

function clientEditModal()
{
    return '
<div id="Client-edit-Modal" class="modal" action="" role="form">
<form id="edit-client-form">
  <h2 class="modal-title" id="edit-client-title">Изменить данные клиента</h2>
  <div class="modal-inputs">
  <p style="grid-column-start: 1;grid-column-end: 3;">
  Имя
  <input id="editFirstNameField" data-validation="required length" data-validation-length="min3" placeholder="Имя" type="text" name="name">
  </p>
  <p>
  Фамилия
  <input id="editLastNameField"  placeholder="Фамилия" type="text" name="lastName">
  </p>

  
  <p>
    ID, цифровой логин
  <input id="editBynameField"  placeholder="Логин" type="text" name="">
  <button type="button" class="genid">Ген</button>
  </p>
    <p>
  Пароль клиента
  <input id="editPasswordField"  placeholder="Пароль" type="text" name="">
    <button type="button" class="genpass">Ген</button>
  </p>
  <p style="padding-top: 15px">
  <span>Страничка оплаты</span>
    <input id="pay_page" type="checkbox" name="lastName">
   <span> Оплата в долг</span>
  <input id="pay_in_debt"  type="checkbox" name="lastName">
  <span style="display:none;">Оплата платежкой</span>
  <input style="display:none;" id="payment_system" type="checkbox" name="lastName">
</p>
   <p>
   Номер телефона
  <input id="editPhoneField" placeholder="Телефон" type="text" name="phone">
  </p>
   <p>
  Макс долг (деньги)
  <input id="editMaxDebtField" placeholder="Макс долг" type="number" name="lastName">
  </p>
  <p>
   Телеграм
  <input id="editTgField" placeholder="Телеграм" type="text" name="tg">
  </p>
  <p>
  Email
  <input id="editEmailField"   placeholder="Email" type="email" name="email">
  </p>
  <p>
  Описание
  <textarea id="editDescriptionField" rows="3"   placeholder="Описание" type="text" name="description"></textarea>
  </p>
 
  </div>
  <input class="modal-submit" type="submit" value="Сохранить">
  </form>
</div>';
}

function clientInfoModal()
{
    return '<a href="#Client-info-modal" rel="modal:open" style="display: none"></a>
<div id="Client-info-modal" class="modal" style="width: auto; text-align:center">
<div id="info-client-form">
  <h2 class="modal-title">Иноформация про клиента</h2>
  <div class="text">
  </div>
  </div>
</div>';
}