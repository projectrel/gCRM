<?php
if (isset($_POST['owner_id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $owner_id = clean($_POST['branch_id']);
    $owner_data = mysqli_fetch_assoc($connection->query("
            SELECT branch_name AS 'name', branch_id AS `id`
            FROM owners 
            WHERE owner_id = '$owner_id'
            "));

    if ($owner_data) {
        echo json_encode($owner_data);
        return false;
    } else {
        return error("failed");
    }
}
