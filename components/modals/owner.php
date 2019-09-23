<?php
function ownerAddModal($data)
{
    if ($data) {
        $i = 0;
        while ($new = $data['branches']->fetch_array()) {
            $branches[$i] = $new;
            $i++;
        }
        $i = 0;
        while ($new = $data['users']->fetch_array()) {
            $users[$i] = $new;
            $i++;
        }
    }
    if (!$branches) return '<div id="Owner-Modal" class="modal" action="">
<h2 class="no-payroll-text">Сначала добавьте предприятие!</h2>
</div>';

    $output = '
<div id="Owner-Modal" class="modal" action="" role="form">
<form id="add-owner-form">
  <h2 class="modal-title">Добавить владельца</h2>
  <div class="modal-inputs">
  <p>
  <select id="nameField" data-validation="required">
  <option value="" disabled selected>Выберите человека</option>';
    if($users) foreach ($users as $key => $var) {
        $output .= '<option value="' . $var['user_id'].'">' . $var['first_name']." ". $var['last_name'] . '</option>';
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
