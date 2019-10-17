<?php
define("TARGET_DIR", $_SERVER['DOCUMENT_ROOT'] . "/api/report/space/");
define("DATE_FORMAT", "Y_m_d___H-i-s");
define("ROW_HEIGHT", 100);
define("COLUMN_WIDTH", 15);
define("EMPTY_CELL", "--------");

define("EFFECTED_COLUMNS", ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']);


define("FIRST_SECTION_TITLE", "Оборотка по VG");
define("SECOND_SECTION_TITLE", "Оборотка по расходам денег");
define("THIRD_SECTION_TITLE", "Оборотка по методам оплаты");
define("FOURTH_SECTION_TITLE", "Оборотка по долгам");



define("FIRST_SECTION_COLOR", "DAF7A6");
define("SECOND_SECTION_COLOR", "О");
define("THIRD_SECTION_COLOR", "О");
define("FOURTH_SECTION_COLOR", "О");



define("FIRST_SECTION_HEADERS", ["Остаток VG на момент предыдущего отчета расчетный", "Остаток VG на момент предыдущего отчета по АПИ", "Долг в валюте по VG расчетный на момент предыдущего отчета",
    "Остаток VG на сейчас расчетный", "Остаток VG на сейчас по АПИ(тот, что считает система)", "Закуплено VG, за неделю, кол-во", "Долг в валюте по VG расчетный на сейчас",
    "Продано VG всего, кол-во ВГ", "Сумма продажи в валюте", "Рефералы"]);
define("SECOND_SECTION_HEADERS", []);
define("THIRD_SECTION_HEADERS", []);
define("FOURTH_SECTION_HEADERS", []);
