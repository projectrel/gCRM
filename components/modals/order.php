<?php

function orderAddModal($data, $more_data)
{
    if ($more_data) {
        $i = 0;
        if ($more_data['owners']) while ($new = $more_data['owners']->fetch_array()) {
            $copy_of_data[$i] = $new;
            $i++;
        }
    }
    if (!$copy_of_data) return '<div id="Order-Modal" class="modal" action="" role="form">
<h2 class="no-payroll-text">Сначало добавьте владельцев</h2>
</div>';
    $output = '
<div id="Order-Modal" class="modal" action="" role="form">
<form id="add-order-form">
  <h2 class="modal-title">Добавить продажу</h2>
  <div class="modal-inputs">
  <p>
  Клиент
<select id="clientField" data-validation="required">
  <option value="" disabled selected>Выберите клиента</option>
  <option class="new-client-option" value="-1">Добавить нового</option>';
    foreach ($more_data['clients'] as $key => $var) {
        $output .= '<option value="' . $var["id"] . '">' . $var["name"] . '</option>';
    }
    $output .= '
  </select>
</p>
<p>
ВГ
<select id="vgField" data-validation="required"><option value="" disabled selected>Выберите валюту</option>';
    if($more_data['vgs']){
        foreach ($more_data['vgs'] as $key => $var) {
            $output .= '<option percent="' . $var["out_percent"] . '" value="' . $var['vg_data_id'] . '">' . $var['name'] . '</option>';
        }
    }


    $output .= '
</select>
</p>
<p>
Валюта
<select id="fiatField" data-validation="required"><option value="" disabled selected>Выберите валюту</option>';
    foreach ($more_data['fiat'] as $key => $var) {
        $output .= '<option value="' . $var["fiat_id"] . '">' . $var['full_name'] . '</option>';
    }
    $output .= '

</select>
</p>
  <p>
  Сумма вг
  <input id="sumVGField" min=0 data-validation="required length" data-validation-length="min1" placeholder="Кол-во виртуальной валюты" type="number" step="0.01" name="sum-vg">
  </p>
  <p>
  Логин клиента
  <input id="loginByVgField" min=0 data-validation="required length" data-validation-length="min1" placeholder="Логин по вг" name="login-vg">
  </p>
   <p>
   Не оплаченая часть
  <input id="debtClField" min=0 type="number" name="debt-sum" value = 0 step="0.01">
  </p>
  <p>
  Реферал
<select id="callmasterField">
  <option value="" selected>Выберите реферала(опц)</option>';
    if($more_data['clients']){
        foreach ($more_data['clients'] as $key => $var) {
            $output .= '<option value="' . $var["id"] . '">' . $var["name"] . '</option>';
        }
    }
    $output .= '
  </select>
</p>
 <p>
 Комментарий
  <textarea id="commentField" rows="3"  placeholder="Комментарий" name="description"></textarea>
  </p>
  <p>
  Продажа
  <input id="outField" min=0 data-validation="required length" data-validation-length="min1" placeholder="Продажа %" type="number" name="out" step="0.01">
  </p>
    <p>
    Способ получения
  <select id="obtainingField" data-validation="required">;
    <option selected disabled >Выберите способ</option>';
    if (isset($more_data['methods']))
        foreach ($more_data['methods'] as $key => $var) {
            $output .= '<option value="' . $var["method_id"] . '">' . $var["method_name"] . '</option>';
        }
    $output .= '
  </select>
  </p>
   </div><div id="owners-lists-container"></div>
   <div id="rollbacks-lists-container" class="modal-inputs" style="display: none">
<p>
   Откат
  <input id="rollback1Field" min="0" placeholder="Откат 1 %" type="number" name="rollback-1" step="0.01">
  </p>
  </div>
  <input class="modal-submit" type="submit" value="Оформить">
  </form>
</div>';
    //edit modal
    session_start();
    if (iCan(2)) {
        $output .= orderEditModal($more_data);
    }
    $output .= orderInfoModal();
    $output .= orderTransactionInfoModal();
    $output .= orderSumChangedModal();
    $output .= noOrderOwnersModal();
    return $output;
}

function orderEditModal($more_data)
{
    $output = '
<div id="Order-edit-Modal" class="modal" action="" role="form">
<form id="edit-order-form">
  <h2 class="modal-title" id="edit-order-title">Редактировать данные продажи</h2>
  <div class="modal-inputs">
  <p>
  Клиент
<select id="editClientField" data-validation="required">
  <option value="" disabled>Выберите клиента</option>
  <option class="new-client-option" value="-1">Добавить нового</option>';
    foreach ($more_data['clients'] as $key => $var) {
        $output .= '<option value="' . $var["id"] . '">' . $var["name"] . '</option>';
    }
    $output .= '
  </select>
</p>
<p>
ВГ
<select id="editVgField" data-validation="required"><option value="" disabled selected>Выберите валюту</option>';
    if($more_data['vgs']){
        foreach ($more_data['vgs'] as $key => $var) {
            $output .= '<option percent="' . $var["out_percent"] . '" value="' . $var['vg_data_id'] . '">' . $var['name'] . '</option>';
        }
    }

    $output .= '
</select>
</p>
<p>
Валюта
<select id="editFiatField" data-validation="required"><option value="" disabled selected>Выберите валюту</option>';
    if($more_data['fiat']){
        foreach ($more_data['fiat'] as $key => $var) {
            $output .= '<option value="' . $var["fiat_id"] . '">' . $var['full_name'] . '</option>';
        }
    }
    $output .= '
</select>
</p>
  <p>
  Сумма вг
  <input id="editSumVGField" min="0" data-validation="required length" data-validation-length="min1" placeholder="Кол-во виртуальной валюты" type="number" step="0.01" name="sum-vg">
  </p>
   <p>
   Не оплаченая часть
  <input id="editDebtClField" min="0" type="number" step="0.01" name="debt-sum" value = 0>
  </p>
  <p>
  Реферал
<select id="editCallmasterField">
  <option value="" selected>Выберите реферала(опц)</option>';
    foreach ($more_data['clients'] as $key => $var) {
        $output .= '<option value="' . $var["id"] . '">' . $var["name"] . '</option>';
    }
    $output .= '
  </select>
</p>
 <p>
 Комментарий
  <textarea id="editCommentField" rows="3"  placeholder="Комментарий" name="description"></textarea>
  </p>
  <p>
  Продажа
  <input id="editOutField" min=0 data-validation="required length" data-validation-length="min1" step="0.01" placeholder="Продажа %" type="number" name="out" step=0.01>
  </p>
  <p>
   Откат
  <input id="editRollback1Field" min="0" placeholder="Откат 1 %" type="number" name="rollback-1" step="0.01">
  </p>
    <p>
    Способ получения
  <select id="editObtainingField" data-validation="required">
    <option value="" disabled selected>Выберите способ</option>';
    if (isset($more_data['methods']))
        foreach ($more_data['methods'] as $key => $var) {
            $output .= '<option value="' . $var["method_id"] . '">' . $var["method_name"] . '</option>';
        }
    $output .= '
  </select>
  </p>
  
  </div>
  <h2>Владельцы</h2>
  <div id="edit-owners-list-visible" class="orders-modal-owners-list"></div>
  <div id="open-invisible-owner-edit-list">Показать всех</div>
  <div id="edit-owners-list-invisible" class="orders-modal-owners-list"></div>
  <input class="modal-submit" type="submit" value="Сохранить">
  </form>
</div>';
    return $output;
}

function orderInfoModal()
{
    return '<a href="#Order-info-modal" rel="modal:open" style="display: none"></a>
<div id="Order-info-modal" class="modal">
<div id="info-order-form">
  <h2 class="modal-title">Иноформация про продажу</h2>
  <div class="text">
  </div>
  </div>
</div>';
}

function orderTransactionInfoModal()
{
    return '<a href="#Order-transaction-info-modal" rel="modal:open" style="display: none"></a>
<div id="Order-transaction-info-modal" class="modal">
 <h2>Выполните вручную!</h2>
 <div class="error-url-box">
 <span>Ссылка: </span><a target="_blank" id="error-url"></a>
</div>
 <button id="copy-btn">Копировать</button>
</div>';
}

function orderSumChangedModal()
{
    return '<div id="Order-sum-changed-modal" class="modal">
 <h3>Выполните транзакцию вручную</h3>
 <div class="sum-change-box">
 <div><span>Было: </span><span class="old-sum"></span></div>
 <div><span>Текущая сумма: </span><span class="new-sum">saddas</span></div>
</div>
</div>';
}

function noOrderOwnersModal()
{
    return '<div id="noOwners-Modal" class="modal" action="">
<h2 class="no-owners-text">Для создания продажи требуется наличие валедльцев!</h2>
</div>';
}