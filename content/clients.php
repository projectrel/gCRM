<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';
include_once '../db.php';
$options['text'] = 'Клиенты';
$options['type'] = 'Client';
$options['edit'] = 1;
$options['info'] = true;
$options['btn-max'] = 2;
$options['btn-text'] = 'Добавить';
$options['btn'] = 1;
session_start();
switch(accessLevel()){
    case 3:
        $clients = $connection->query('
                    SELECT client_id AS `id`, concat(first_name, " ", last_name) AS "Полное имя",
                           concat(C.login, " ")  AS `Логин`, IFNULL(phone_number, "-") AS `телефон`,
                           email AS `почта`, IFNULL(telegram, "-") AS `телеграм`, `password` AS пароль, max_debt AS "макс. долг",  CASE WHEN pay_page = 0 THEN "отключена" ELSE "подключена" END AS "Стр. оплаты",  CASE WHEN pay_in_debt = 0 THEN "отключена" ELSE "подключена" END AS "Оплата в долг", CASE WHEN payment_system = 0 THEN "отключена" ELSE "подключена" END AS "Платежка"
                    FROM clients C
                    ');
        break;
    case 2:
    case 1:
        $clients = $connection->query('
                    SELECT DISTINCT C.client_id AS `id`, concat(C.first_name, " ", C.last_name) AS "Полное имя",
                                    concat(C.login, " ") AS `Логин`, IFNULL(phone_number, "-") AS `телефон`,
                                    C.email AS `почта`, IFNULL(telegram, "-") AS `телеграм`, `password` AS пароль,  max_debt AS "макс. долг",
                                    CASE WHEN pay_page = 0 THEN "отключена" ELSE "подключена" END AS "Стр. оплаты",  CASE WHEN pay_in_debt = 0 THEN "отключена" ELSE "подключена" END AS "Оплата в долг", CASE WHEN payment_system = 0 THEN "отключена" ELSE "подключена" END AS "Платежка"
                    FROM clients C
                    WHERE user_id IN (
                        SELECT user_id FROM users WHERE branch_id = '.$_SESSION['branch_id'].'
                    )
                    ');
        break;
//    case 1:
//        $clients = $connection->query('
//                    SELECT DISTINCT C.client_id AS `id`, concat(C.last_name, " ", C.first_name) AS "Полное имя",
//                                    C.byname AS `Имя`, IFNULL(phone_number, "-") AS `телефон`, C.email AS `почта`, IFNULL(telegram, "-") AS `телеграм`
//          FROM clients C
//                    WHERE user_id = '.$_SESSION['id'].'
//                    ');
}
echo template(display_data($clients, $options));
?>
