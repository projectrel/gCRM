<?php

include_once("../../funcs.php");
if (isset($_POST['fiat'])) {
    include_once("../../db.php");
    $fiat = clean($_POST['fiat']);
    $fiat_data = mysqli_fetch_assoc($connection->query("
    SELECT * FROM fiats WHERE fiat_id = '$fiat'
    "));
} else {
    $fiat_data = mysqliToArray($connection->query("
    SELECT * FROM fiats 
    "));
}
if ($fiat_data) {
    echo json_encode($fiat_data);
    return false;
} else {
    return error("failed");
}



