-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Окт 09 2019 г., 10:07
-- Версия сервера: 5.6.41
-- Версия PHP: 5.6.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `crm6.empty`
--

-- --------------------------------------------------------

--
-- Структура таблицы `branch`
--

CREATE TABLE `branch` (
  `branch_id` int(11) NOT NULL,
  `branch_name` varchar(40) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `ik_id` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `branch`
--

INSERT INTO `branch` (`branch_id`, `branch_name`, `active`, `ik_id`) VALUES
(1, 'MAIN', 1, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `changes`
--

CREATE TABLE `changes` (
  `change_id` int(11) NOT NULL,
  `change_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `change_user_id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `fiat_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `method_id` int(11) DEFAULT NULL,
  `outgo_type_id` varchar(200) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vg_data_id` int(11) DEFAULT NULL,
  `vg_id` int(11) DEFAULT NULL,
  `vg_purchase_id` int(11) DEFAULT NULL,
  `outgo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clients`
--

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `byname` varchar(20) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `telegram` varchar(30) DEFAULT NULL,
  `max_debt` decimal(10,2) DEFAULT '0.00',
  `password` varchar(20) DEFAULT NULL,
  `pay_page` tinyint(1) NOT NULL DEFAULT '1',
  `pay_in_debt` tinyint(1) NOT NULL DEFAULT '1',
  `payment_system` tinyint(1) NOT NULL DEFAULT '1',
  `login` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `debts_processing_reports`
--

CREATE TABLE `debts_processing_reports` (
  `debt_processing_report_id` int(11) NOT NULL,
  `debt_processing_report_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vg_data_id` int(11) NOT NULL,
  `fiat_id` int(11) NOT NULL,
  `debt_processing_report_sum` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `debt_history`
--

CREATE TABLE `debt_history` (
  `debt_history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `debt_sum` decimal(20,2) NOT NULL,
  `date` datetime NOT NULL,
  `method_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fiats`
--

CREATE TABLE `fiats` (
  `fiat_id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `name` varchar(5) NOT NULL,
  `full_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `income_history`
--

CREATE TABLE `income_history` (
  `income_id` int(11) NOT NULL,
  `sum` decimal(15,2) NOT NULL,
  `method_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `methods_of_obtaining`
--

CREATE TABLE `methods_of_obtaining` (
  `method_id` int(11) NOT NULL,
  `method_name` varchar(40) NOT NULL,
  `participates_in_balance` smallint(6) NOT NULL DEFAULT '1',
  `is_active` smallint(6) NOT NULL DEFAULT '1',
  `branch_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `methods_processing_reports`
--

CREATE TABLE `methods_processing_reports` (
  `methods_processing_report_id` int(11) NOT NULL,
  `methods_processing_report_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `method_id` int(11) NOT NULL,
  `fiat_id` int(11) NOT NULL,
  `methods_processing_report_fiat_income` decimal(15,2) NOT NULL,
  `methods_processing_report_fiat_outgo` decimal(15,2) NOT NULL,
  `methods_processing_report_fiat_diff` decimal(15,2) NOT NULL,
  `methods_processing_report_debtors_debt` decimal(15,2) NOT NULL COMMENT 'Клиенты(предприятию',
  `methods_processing_report_rollback_debt` decimal(15,2) NOT NULL COMMENT 'Клиентам',
  `methods_processing_report_owners_profit_debt` decimal(15,2) NOT NULL COMMENT 'Владельцам'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `vg_data_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `sum_vg` decimal(10,0) NOT NULL,
  `real_out_percent` float(15,2) NOT NULL,
  `sum_currency` decimal(15,2) NOT NULL,
  `method_id` int(11) DEFAULT '1',
  `rollback_sum` decimal(15,2) NOT NULL,
  `rollback_1` float(15,2) NOT NULL,
  `date` datetime NOT NULL,
  `callmaster` int(11) DEFAULT NULL,
  `order_debt` int(11) NOT NULL DEFAULT '0',
  `description` varchar(500) DEFAULT NULL,
  `fiat_id` int(11) NOT NULL DEFAULT '1',
  `loginByVg` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `outgo`
--

CREATE TABLE `outgo` (
  `outgo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_as_owner_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `method_id` int(11) DEFAULT NULL,
  `outgo_type_id` varchar(11) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sum` decimal(11,2) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `vg_data_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `outgoes_processing_reports`
--

CREATE TABLE `outgoes_processing_reports` (
  `outgoes_processing_report_id` int(11) NOT NULL,
  `outgoes_processing_report_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `outgo_type_id` varchar(200) NOT NULL,
  `method_id` int(11) NOT NULL,
  `fiat_id` int(11) NOT NULL,
  `outgo_processing_report_sum` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `outgo_types`
--

CREATE TABLE `outgo_types` (
  `outgo_type_id` varchar(11) NOT NULL,
  `outgo_name` varchar(40) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `outgo_types`
--

INSERT INTO `outgo_types` (`outgo_type_id`, `outgo_name`, `branch_id`, `active`) VALUES
('0', 'Закупка VG', 1, 1),
('1', 'root_type', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `outgo_types_relative`
--

CREATE TABLE `outgo_types_relative` (
  `parent_id` varchar(11) NOT NULL,
  `son_id` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `fiat_id` int(11) NOT NULL,
  `sum` decimal(15,2) NOT NULL,
  `client_rollback_id` int(11) DEFAULT NULL,
  `client_debt_id` int(11) DEFAULT NULL,
  `vg_data_debt_id` int(11) DEFAULT NULL,
  `method_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `project_name` varchar(40) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `report_shares`
--

CREATE TABLE `report_shares` (
  `report_share_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sum_currency` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `rollback_paying`
--

CREATE TABLE `rollback_paying` (
  `rollack_paying_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `rollback_sum` decimal(10,2) NOT NULL,
  `date` datetime NOT NULL,
  `method_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `shares`
--

CREATE TABLE `shares` (
  `shares_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_as_owner_id` int(11) NOT NULL,
  `sum` decimal(15,2) NOT NULL,
  `share_percent` decimal(15,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(15) DEFAULT NULL,
  `last_name` varchar(15) NOT NULL,
  `role` varchar(30) NOT NULL,
  `branch_id` int(30) NOT NULL,
  `pass_hash` varchar(100) DEFAULT NULL,
  `login` varchar(100) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `is_owner` tinyint(1) NOT NULL DEFAULT '0',
  `telegram` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `role`, `branch_id`, `pass_hash`, `login`, `active`, `is_owner`, `telegram`) VALUES
(2, 'Test', 'Moder', 'moder', 1, '$2y$10$2zCpMmzWdYudw5LkeSSq7.ZK26fup.eAU5h3aZk3WyOHZU/J/1EP2', 'moder', 1, 0, 'asdffs');

-- --------------------------------------------------------

--
-- Структура таблицы `vg_data`
--

CREATE TABLE `vg_data` (
  `vg_data_id` int(11) NOT NULL,
  `vg_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `vg_amount` int(11) NOT NULL,
  `vg_api_amount` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `api_url_regexp` varchar(300) DEFAULT NULL,
  `access_key` varchar(100) DEFAULT NULL,
  `out_percent` decimal(15,2) NOT NULL,
  `in_percent` decimal(15,2) NOT NULL,
  `vg_data_login` varchar(100) DEFAULT NULL,
  `vg_data_balance_control` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `vg_processing_reports`
--

CREATE TABLE `vg_processing_reports` (
  `vg_processing_report_id` int(11) NOT NULL,
  `fiat_id` int(11) NOT NULL,
  `vg_data_id` int(11) NOT NULL,
  `vg_balance` int(20) NOT NULL,
  `vg_api_balance` int(20) NOT NULL,
  `fiat_debt` int(20) NOT NULL,
  `vg_purchased` int(20) NOT NULL,
  `vg_sold` int(20) NOT NULL,
  `vg_sold_in_fiat` int(20) NOT NULL,
  `vg_callmasters_sum` int(20) NOT NULL,
  `vg_processing_report_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `vg_purchases`
--

CREATE TABLE `vg_purchases` (
  `vg_purchase_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vg_data_id` int(11) NOT NULL,
  `fiat_id` int(11) NOT NULL,
  `vg_purchase_sum` decimal(15,2) NOT NULL,
  `vg_purchase_sum_currency` decimal(15,2) NOT NULL,
  `vg_purchase_credit` decimal(15,2) NOT NULL,
  `vg_purchase_on_credit` tinyint(1) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vg_purchase_unique_key` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `virtualgood`
--

CREATE TABLE `virtualgood` (
  `vg_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`branch_id`);

--
-- Индексы таблицы `changes`
--
ALTER TABLE `changes`
  ADD PRIMARY KEY (`change_id`),
  ADD KEY `last_change_user_id` (`change_user_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `fiat_id` (`fiat_id`),
  ADD KEY `method_id` (`method_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `last_changes_ibfk_9` (`user_id`),
  ADD KEY `vg_data_id` (`vg_data_id`),
  ADD KEY `vg_id` (`vg_id`),
  ADD KEY `vg_purchase_id` (`vg_purchase_id`),
  ADD KEY `outgo_type_id` (`outgo_type_id`),
  ADD KEY `outgo_id` (`outgo_id`);

--
-- Индексы таблицы `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `debts_processing_reports`
--
ALTER TABLE `debts_processing_reports`
  ADD PRIMARY KEY (`debt_processing_report_id`),
  ADD KEY `fiat_id` (`fiat_id`),
  ADD KEY `vg_data_id` (`vg_data_id`);

--
-- Индексы таблицы `debt_history`
--
ALTER TABLE `debt_history`
  ADD PRIMARY KEY (`debt_history_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fiat_id` (`method_id`);

--
-- Индексы таблицы `fiats`
--
ALTER TABLE `fiats`
  ADD PRIMARY KEY (`fiat_id`);

--
-- Индексы таблицы `income_history`
--
ALTER TABLE `income_history`
  ADD PRIMARY KEY (`income_id`),
  ADD KEY `fiat` (`method_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `owner_id` (`owner_id`) USING BTREE;

--
-- Индексы таблицы `methods_of_obtaining`
--
ALTER TABLE `methods_of_obtaining`
  ADD PRIMARY KEY (`method_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Индексы таблицы `methods_processing_reports`
--
ALTER TABLE `methods_processing_reports`
  ADD PRIMARY KEY (`methods_processing_report_id`),
  ADD KEY `fiat_id` (`fiat_id`),
  ADD KEY `method_id` (`method_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `vg_id` (`client_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `callmaster` (`callmaster`),
  ADD KEY `fiat_id` (`fiat_id`),
  ADD KEY `method_id` (`method_id`),
  ADD KEY `vg_data_id` (`vg_data_id`);

--
-- Индексы таблицы `outgo`
--
ALTER TABLE `outgo`
  ADD PRIMARY KEY (`outgo_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `fiat_id` (`method_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `outgo_type_id` (`outgo_type_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_as_owner_id` (`user_as_owner_id`),
  ADD KEY `vg_data_id` (`vg_data_id`);

--
-- Индексы таблицы `outgoes_processing_reports`
--
ALTER TABLE `outgoes_processing_reports`
  ADD PRIMARY KEY (`outgoes_processing_report_id`),
  ADD KEY `fiat_id` (`fiat_id`),
  ADD KEY `outgo_type_id` (`outgo_type_id`),
  ADD KEY `method_id` (`method_id`);

--
-- Индексы таблицы `outgo_types`
--
ALTER TABLE `outgo_types`
  ADD PRIMARY KEY (`outgo_type_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Индексы таблицы `outgo_types_relative`
--
ALTER TABLE `outgo_types_relative`
  ADD PRIMARY KEY (`parent_id`,`son_id`),
  ADD KEY `son_id` (`son_id`);

--
-- Индексы таблицы `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `client_debt_id` (`client_debt_id`),
  ADD KEY `client_rollback_id` (`client_rollback_id`),
  ADD KEY `fiat_id` (`fiat_id`),
  ADD KEY `vg_data_id` (`vg_data_debt_id`),
  ADD KEY `method_id` (`method_id`);

--
-- Индексы таблицы `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Индексы таблицы `report_shares`
--
ALTER TABLE `report_shares`
  ADD PRIMARY KEY (`report_share_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `rollback_paying`
--
ALTER TABLE `rollback_paying`
  ADD PRIMARY KEY (`rollack_paying_id`),
  ADD UNIQUE KEY `rollack_paying_id` (`rollack_paying_id`),
  ADD UNIQUE KEY `rollack_paying_id_2` (`rollack_paying_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `Rollback_paying_ibfk_22` (`method_id`);

--
-- Индексы таблицы `shares`
--
ALTER TABLE `shares`
  ADD PRIMARY KEY (`shares_id`),
  ADD KEY `owner_id` (`user_as_owner_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Индексы таблицы `vg_data`
--
ALTER TABLE `vg_data`
  ADD PRIMARY KEY (`vg_data_id`),
  ADD KEY `vg_id` (`vg_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Индексы таблицы `vg_processing_reports`
--
ALTER TABLE `vg_processing_reports`
  ADD PRIMARY KEY (`vg_processing_report_id`),
  ADD KEY `fiat_id` (`fiat_id`),
  ADD KEY `vg_data_id` (`vg_data_id`);

--
-- Индексы таблицы `vg_purchases`
--
ALTER TABLE `vg_purchases`
  ADD PRIMARY KEY (`vg_purchase_id`),
  ADD UNIQUE KEY `vg_purchase_unique_key` (`vg_purchase_unique_key`),
  ADD KEY `vg_purchases_ibfk_1` (`vg_data_id`),
  ADD KEY `fiat_id` (`fiat_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `virtualgood`
--
ALTER TABLE `virtualgood`
  ADD PRIMARY KEY (`vg_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `branch`
--
ALTER TABLE `branch`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `changes`
--
ALTER TABLE `changes`
  MODIFY `change_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `debts_processing_reports`
--
ALTER TABLE `debts_processing_reports`
  MODIFY `debt_processing_report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `debt_history`
--
ALTER TABLE `debt_history`
  MODIFY `debt_history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `fiats`
--
ALTER TABLE `fiats`
  MODIFY `fiat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `income_history`
--
ALTER TABLE `income_history`
  MODIFY `income_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `methods_of_obtaining`
--
ALTER TABLE `methods_of_obtaining`
  MODIFY `method_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `methods_processing_reports`
--
ALTER TABLE `methods_processing_reports`
  MODIFY `methods_processing_report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `outgo`
--
ALTER TABLE `outgo`
  MODIFY `outgo_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `outgoes_processing_reports`
--
ALTER TABLE `outgoes_processing_reports`
  MODIFY `outgoes_processing_report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `report_shares`
--
ALTER TABLE `report_shares`
  MODIFY `report_share_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `rollback_paying`
--
ALTER TABLE `rollback_paying`
  MODIFY `rollack_paying_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `shares`
--
ALTER TABLE `shares`
  MODIFY `shares_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `vg_data`
--
ALTER TABLE `vg_data`
  MODIFY `vg_data_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vg_processing_reports`
--
ALTER TABLE `vg_processing_reports`
  MODIFY `vg_processing_report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `vg_purchases`
--
ALTER TABLE `vg_purchases`
  MODIFY `vg_purchase_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `virtualgood`
--
ALTER TABLE `virtualgood`
  MODIFY `vg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `changes`
--
ALTER TABLE `changes`
  ADD CONSTRAINT `changes_ibfk_1` FOREIGN KEY (`change_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `changes_ibfk_10` FOREIGN KEY (`vg_data_id`) REFERENCES `vg_data` (`vg_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `changes_ibfk_11` FOREIGN KEY (`vg_id`) REFERENCES `virtualgood` (`vg_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `changes_ibfk_12` FOREIGN KEY (`vg_purchase_id`) REFERENCES `vg_purchases` (`vg_purchase_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `changes_ibfk_13` FOREIGN KEY (`outgo_type_id`) REFERENCES `outgo_types` (`outgo_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `changes_ibfk_14` FOREIGN KEY (`outgo_id`) REFERENCES `outgo` (`outgo_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `changes_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `changes_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `changes_ibfk_4` FOREIGN KEY (`fiat_id`) REFERENCES `fiats` (`fiat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `changes_ibfk_5` FOREIGN KEY (`method_id`) REFERENCES `methods_of_obtaining` (`method_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `changes_ibfk_6` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `changes_ibfk_7` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `changes_ibfk_8` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `changes_ibfk_9` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `debts_processing_reports`
--
ALTER TABLE `debts_processing_reports`
  ADD CONSTRAINT `debts_processing_reports_ibfk_1` FOREIGN KEY (`fiat_id`) REFERENCES `fiats` (`fiat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `debts_processing_reports_ibfk_2` FOREIGN KEY (`vg_data_id`) REFERENCES `vg_data` (`vg_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `debt_history`
--
ALTER TABLE `debt_history`
  ADD CONSTRAINT `debt_history_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `debt_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `debt_history_ibfk_3` FOREIGN KEY (`method_id`) REFERENCES `methods_of_obtaining` (`method_id`);

--
-- Ограничения внешнего ключа таблицы `income_history`
--
ALTER TABLE `income_history`
  ADD CONSTRAINT `income_history_ibfk_1` FOREIGN KEY (`method_id`) REFERENCES `methods_of_obtaining` (`method_id`),
  ADD CONSTRAINT `income_history_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `income_history_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `methods_of_obtaining`
--
ALTER TABLE `methods_of_obtaining`
  ADD CONSTRAINT `methods_of_obtaining_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `methods_processing_reports`
--
ALTER TABLE `methods_processing_reports`
  ADD CONSTRAINT `methods_processing_reports_ibfk_1` FOREIGN KEY (`fiat_id`) REFERENCES `fiats` (`fiat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `methods_processing_reports_ibfk_2` FOREIGN KEY (`method_id`) REFERENCES `methods_of_obtaining` (`method_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `Order_ibfk_4` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`callmaster`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`fiat_id`) REFERENCES `fiats` (`fiat_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`method_id`) REFERENCES `methods_of_obtaining` (`method_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_4` FOREIGN KEY (`vg_data_id`) REFERENCES `vg_data` (`vg_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `outgo`
--
ALTER TABLE `outgo`
  ADD CONSTRAINT `outgo_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `outgo_ibfk_2` FOREIGN KEY (`method_id`) REFERENCES `methods_of_obtaining` (`method_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `outgo_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `outgo_ibfk_4` FOREIGN KEY (`outgo_type_id`) REFERENCES `outgo_types` (`outgo_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `outgo_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `outgo_ibfk_6` FOREIGN KEY (`user_as_owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `outgo_ibfk_7` FOREIGN KEY (`vg_data_id`) REFERENCES `vg_data` (`vg_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `outgoes_processing_reports`
--
ALTER TABLE `outgoes_processing_reports`
  ADD CONSTRAINT `outgoes_processing_reports_ibfk_1` FOREIGN KEY (`fiat_id`) REFERENCES `fiats` (`fiat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `outgoes_processing_reports_ibfk_2` FOREIGN KEY (`outgo_type_id`) REFERENCES `outgo_types` (`outgo_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `outgoes_processing_reports_ibfk_3` FOREIGN KEY (`method_id`) REFERENCES `methods_of_obtaining` (`method_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `outgo_types`
--
ALTER TABLE `outgo_types`
  ADD CONSTRAINT `outgo_types_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `outgo_types_relative`
--
ALTER TABLE `outgo_types_relative`
  ADD CONSTRAINT `outgo_types_relative_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `outgo_types` (`outgo_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `outgo_types_relative_ibfk_2` FOREIGN KEY (`son_id`) REFERENCES `outgo_types` (`outgo_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`client_debt_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`client_rollback_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `payments_ibfk_4` FOREIGN KEY (`fiat_id`) REFERENCES `fiats` (`fiat_id`),
  ADD CONSTRAINT `payments_ibfk_5` FOREIGN KEY (`vg_data_debt_id`) REFERENCES `vg_data` (`vg_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_ibfk_6` FOREIGN KEY (`method_id`) REFERENCES `methods_of_obtaining` (`method_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `report_shares`
--
ALTER TABLE `report_shares`
  ADD CONSTRAINT `report_shares_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `rollback_paying`
--
ALTER TABLE `rollback_paying`
  ADD CONSTRAINT `Rollback_paying_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `Rollback_paying_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `Rollback_paying_ibfk_22` FOREIGN KEY (`method_id`) REFERENCES `methods_of_obtaining` (`method_id`);

--
-- Ограничения внешнего ключа таблицы `shares`
--
ALTER TABLE `shares`
  ADD CONSTRAINT `shares_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `shares_ibfk_3` FOREIGN KEY (`user_as_owner_id`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`);

--
-- Ограничения внешнего ключа таблицы `vg_data`
--
ALTER TABLE `vg_data`
  ADD CONSTRAINT `vg_data_ibfk_1` FOREIGN KEY (`vg_id`) REFERENCES `virtualgood` (`vg_id`),
  ADD CONSTRAINT `vg_data_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`);

--
-- Ограничения внешнего ключа таблицы `vg_processing_reports`
--
ALTER TABLE `vg_processing_reports`
  ADD CONSTRAINT `vg_processing_reports_ibfk_1` FOREIGN KEY (`fiat_id`) REFERENCES `fiats` (`fiat_id`),
  ADD CONSTRAINT `vg_processing_reports_ibfk_2` FOREIGN KEY (`vg_data_id`) REFERENCES `vg_data` (`vg_data_id`);

--
-- Ограничения внешнего ключа таблицы `vg_purchases`
--
ALTER TABLE `vg_purchases`
  ADD CONSTRAINT `vg_purchases_ibfk_1` FOREIGN KEY (`vg_data_id`) REFERENCES `vg_data` (`vg_data_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vg_purchases_ibfk_2` FOREIGN KEY (`fiat_id`) REFERENCES `fiats` (`fiat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vg_purchases_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
