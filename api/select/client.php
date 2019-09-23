<?php
if (isset($_POST['client_id'])) {
    include_once("../../db.php");
    include_once("../../funcs.php");
    $client_id = clean($_POST['client_id']);
    $client_data = mysqli_fetch_assoc($connection->query("
            SELECT client_id AS `id`,  concat(last_name, ' ', first_name,' (', byname,')') AS `full_name`,
            `first_name`, `last_name`, `login` AS `login`, `phone_number` AS `phone`,
            `description`, `email`, `telegram`, `password`, max_debt, pay_page, payment_system, pay_in_debt
            FROM clients 
            WHERE client_id = '$client_id'
            "));

    if ($client_data) {
        echo json_encode($client_data);
        return false;
    } else {
        error("failed");
        return false;
    }
}
