<?php
if (isset($_POST['method_id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $method_id = clean($_POST['method_id']);
    $method_data = mysqli_fetch_assoc($connection->query("
    SELECT `method_name`, `method_id`
    FROM methods_of_obtaining 
    WHERE  method_id = '$method_id'
    "));
    if ($method_data) {
        echo json_encode($method_data);
        return false;
    } else {
        return error("failed");
    }
}
