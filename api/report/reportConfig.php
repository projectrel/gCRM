<?php
define("TARGET_DIR", $_SERVER['DOCUMENT_ROOT'] . "/report-space/");
define("DATE_FORMAT", "Y_m_d___H-i-s");
define("ROW_HEIGHT", 100);
define("COLUMN_WIDTH", 15);
define("EMPTY_CELL", "--------");

define("EFFECTED_COLUMNS", ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']);


define("FIRST_SECTION_TITLE", "Оборотка по VG");
define("SECOND_SECTION_TITLE", "Оборотка по расходам денег");
define("THIRD_SECTION_TITLE", "Оборотка по методам оплаты");
define("FOURTH_SECTION_TITLE", "Оборотка по долгам");
define("FIFTH_SECTION_TITLE", "Оборотка по валюте");

define("FIRST_SECTION_COLOR", "DAF7A6");
define("SECOND_SECTION_COLOR", "E48989 ");
define("THIRD_SECTION_COLOR", "8E8A8A");
define("FOURTH_SECTION_COLOR", "EEEE12");
define("FIFTH_SECTION_COLOR", "EEEE12");

define("FIRST_SECTION_HEADERS", ["", "Остаток VG на момент предыдущего отчета расчетный", "Остаток VG на момент предыдущего отчета по АПИ", "Долг в валюте по VG расчетный на момент предыдущего отчета",
    "Остаток VG на сейчас расчетный", "Остаток VG на сейчас по АПИ", "Закуплено VG, за неделю, кол - во", "Долг в валюте по VG расчетный на сейчас",
    "Продано VG всего, кол - во ВГ", "Сумма продажи в валюте", "Рефералы"]);
define("SECOND_SECTION_HEADERS", ["Тип расхода", "счет", "Валюта", "Сумма"]);
define("THIRD_SECTION_HEADERS", ["", "Остаток на настоящий момент", "остаток на момент предыдущего отчета", "Расходы фиата", "Продажи(+фиата)", "Разница"]);
define("FOURTH_SECTION_HEADERS", ["VG/валюта", "Сумма"]);
define("FIFTH_SECTION_HEADERS",["Валюта", "Долги нам на настоящий момент", "Невыплаченные откаты", "Невыплаченная прибыль владельцев"]);


//MAIL
define("SMTP_HOST","smtp.gmail.com");
define("SMTP_MAIL_LOGIN", "rshchybryk@gmail.com");
define("SMTP_MAIL_PASS", "roma8121999");
define("SMTP_MAIL_PORT", 587);

define("REPORT_MAIL_WEEKLY_SUBJECT", "Недельный отчет");
define("REPORT_MAIL_WEEKLY_BODY", "Недельный отчет");
define("REPORT_MAIL_SUBJECT", "Отчет");
define("REPORT_MAIL_BODY", "Отчет");