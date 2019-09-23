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
  <option value="" selected disabled>Выберите владельца (опц)</option>
  <option value="branch">Расход предприятия</option>';
    foreach ($more_data['clients'] as $key => $var) {
        $output .= '<option value="' . $var['owner_id'] . '">' . $var['name'] . '</option>';
    }
    $output .= '
</select>
</p>
<p>
  <select id="typeField">
  <option value="" selected disabled>Выберите тип расхода(опц)</option>';
    if($more_data['types']){
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
  <option value="" selected disabled>Выберите проект(опц)</option>';
    if($more_data['projects']){
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
  <select id="fiatField">
  <option data-validation="required" value="" selected disabled>Выберите валюту</option>';
    foreach ($more_data['fiats'] as $key => $var) {
        $output .= '<option value="' . $var['fiat_id'] . '">' . $var['name'] . '</option>';
    }
    $output .= '
</select>
</p>
  </div>
  <input class="modal-submit" type="submit" value="Добавить">
  </form>
</div>';

    return $output;
}