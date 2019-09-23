<?php
if (isset($_POST['order_id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $order_id = clean($_POST['order_id']);
    $order_data = mysqliToArray($connection->query("
        SELECT share_percent, S.sum AS 'sum', concat(O.last_name, ' ', O.first_name) AS `name`, F.full_name AS `fiat`, rollback_1, OD.rollback_sum, concat(C.last_name, ' ', C.first_name) AS `callmaster`
        FROM shares S
        INNER JOIN (SELECT user_id AS `owner_id`, first_name, last_name FROM users WHERE is_owner = 1) O ON O.owner_id = S.user_as_owner_id
        INNER JOIN orders OD ON OD.order_id = S.order_id
        INNER JOIN fiats F ON OD.fiat_id = F.fiat_id
        LEFT JOIN clients C ON C.client_id = OD.callmaster
        WHERE S.order_id='$order_id'
    "));
    if ($order_data) {
        echo json_encode($order_data);
        return false;
    } else {
        error("failed");
        return false;
    }
}
