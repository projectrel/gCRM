<?php
if (isset($_POST['user_id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $user_id = clean($_POST['user_id']);
    $user_data = mysqli_fetch_assoc($connection->query("
            SELECT user_id AS `id`,  concat(last_name, ' ', first_name,' (', login,')') AS `full_name`,
            `first_name`, `last_name`, `login`, `branch_id`, `role`, `telegram`
            FROM users 
            WHERE user_id = '$user_id'
            "));

    if ($user_data) {
        echo json_encode($user_data);
        return false;
    } else {
        error("failed");
        return false;
    }
}
