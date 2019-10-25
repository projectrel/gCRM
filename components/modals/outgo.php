<?php
function outgoModal($data, $more_data)
{

    $output = '
<div id="Outgo-Modal" class="modal" action="" role="form">
<form id="add-outgo-form">
  <h2 class="modal-title">Добавить расходы</h2>
  <div class="modal-inputs">
   <p>
  <input id="sumField" min=0 data-validation="required" placeholder="Сумма" type="number" step="0.01" name="sum">
  </p>
  <p>
  <select id="ownerField">
  <option value="" selected >Выберите владельца (опц)</option>
  <option value="branch">Расход предприятия</option>';
    foreach ($more_data['clients'] as $key => $var) {
        $output .= '<option value="' . $var['owner_id'] . '">' . $var['name'] . '</option>';
    }
    $output .= '
</select>
</p>
<p>
  <select id="typeField">
  <option value="" selected >Выберите тип расхода(опц)</option>';
    if(array_key_exists("types", $more_data)){
        foreach ($more_data['types'] as $key => $var) {
            $spaces = strlen($var['id']) - 1;
            $spacesstr = str_repeat('&nbsp;', $spaces);
            $output .= '<option value="' . $var['id'] . '">' . $spacesstr . $var['name'] . '</option>';
        }
    }
    $output .= '
</select>
</p>
<p>
  <select id="projectField">
  <option value="" selected >Выберите проект(опц)</option>';

    if(array_key_exists("projects", $more_data)){
        foreach ($more_data['projects'] as $key => $var) {
            $output .= '<option value="' . $var['project_id'] . '">' . $var['project_name'] . '</option>';
        }
    }
    $output .= '
</select>
</p>
 <p>
  <textarea id="commentField" rows="3"  placeholder="Комментарий" name="description"></textarea>
  </p>
  <p>
  <select id="methodField">
  <option data-validation="required" value="" selected >Выберите счет</option>';
    foreach ($more_data['methods'] as $key => $var) {
        $output .= '<option value="' . $var['method_id'] . '">' . $var['method_name'] . '</option>';
    }
    $output .= '
</select>
</p>
  </div>
  <input class="modal-submit" type="submit" value="Добавить">
  </form>
</div>';
    if(!isset($_SESSION))
    session_start();
    if (iCan(2)) {
        $output .= outgoEditModal($data,$more_data);
    }
    return $output;
}

function outgoEditModal($data, $more_data)
{
    $output = '
<div id="Outgo-edit-Modal" class="modal" action="" role="form">
<form id="edit-outgo-form">
  <h2 class="modal-title">Редактировать расход</h2>
  <div class="modal-inputs">
   <p>
  <input id="editSumField" min=0 data-validation="required" placeholder="Сумма" type="number" step="0.01" name="sum">
  </p>
  <p>
  <select id="editOwnerField">
  <option value="" selected >Выберите владельца (опц)</option>
  <option value="branch">Расход предприятия</option>';
    foreach ($more_data['clients'] as $key => $var) {
        $output .= '<option value="' . $var['owner_id'] . '">' . $var['name'] . '</option>';
    }
    $output .= '
</select>
</p>
<p>
  <select id="editTypeField">
  <option value="" selected >Выберите тип расхода(опц)</option>';
    if(array_key_exists("types", $more_data)){
        foreach ($more_data['types'] as $key => $var) {
            $spaces = strlen($var['id']) - 1;
            $spacesstr = str_repeat('&nbsp;', $spaces);
            $output .= '<option value="' . $var['id'] . '">' . $spacesstr . $var['name'] . '</option>';
        }
    }
    $output .= '
</select>
</p>
<p>
  <select id="editProjectField">
  <option value="" selected >Выберите проект(опц)</option>';
    if(array_key_exists("projects", $more_data)){
        foreach ($more_data['projects'] as $key => $var) {
            $output .= '<option value="' . $var['project_id'] . '">' . $var['project_name'] . '</option>';
        }
    }
    $output .= '
</select>
</p>
 <p>
  <textarea id="editCommentField" rows="3"  placeholder="Комментарий" name="description"></textarea>
  </p>
  <p>
  <select id="editMethodField">
  <option data-validation="required" value="" selected >Выберите счет</option>';
    foreach ($more_data['methods'] as $key => $var) {
        $output .= '<option value="' . $var['method_id'] . '">' . $var['method_name'] . '</option>';
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