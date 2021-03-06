<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/funcs.php";
$curPage = substr($_SERVER['REQUEST_URI'], 9, -4);
?>
<nav>
    <div id="menu" page="<?php echo $curPage ?>">

        <ul class="outer-list">
            <li>
                <a class="btn-main" id="create-report-btn" href="">Создать отчет
                </a>
            </li>
            <li>
                <a class="btn-main transfer-color" id="transfer-fiat-btn" rel="modal:open" href="#Transfer-Modal">Перевести деньги
                </a>
            </li>
            <li>
                <a href="../.." class=<?php echo(!$curPage || $curPage == "" ? '"active" disabled' : '') ?>>Главная</a>
            </li>
            <li><a href="../../content/clients.php"
                   class=<?php echo($curPage === 'clients' ? '"active" disabled' : '') ?>>Клиенты</a>
            </li>
            <?php if (iCan(2))
                echo '
        <li><a href="../../content/owners.php" class=' . ($curPage === 'owners' ? '"active" disabled' : '') . '>Владельцы</a>
        </li>
        <li><a href="../../content/users.php" class=' . ($curPage === 'users' ? '"active" disabled' : '') .
                    '>Сотрудники</a></li>';
            else echo '' ?>
            <?php if (iCan(3))
                echo '<li><a href="../../content/branches.php"
              class=' . ($curPage === 'branches' ? '"active" disabled' : '') . '>Предприятия</a></li>
        
        <li><a href="../../content/fiats.php" class=' . ($curPage === 'fiats' ? '"active" disabled' : '') . '>Валюты</a>
        </li>';
            else echo '' ?>
            <li class="drop-list"><a>VG <i id="arrow" class="fa fa-arrow-down"></i></a>
                <ul id="drop-down">
                    <?php if (iCan(3))
                        echo '<li><a href="../../content/global.php" class=' . "submenu" . ($curPage === 'global' ? '"active" disabled' : '') . '>Глобальное
                        VG</a></li>';
                    else echo '' ?>
                    <li class="submenu"><a href="../../content/vg-purchases.php">Закупки VG</a></li>
                    <li class="submenu info-submenu"><a href="../../content/vgs.php">Инфо</a></li>
                    <li class="submenu vg-debt-submenu"><a href="../../content/vg-debts.php">Задолженность</a></li>
                    <li class="submenu vg-debt-submenu"><a href="../../content/vg-debts-paybacks.php">Выплаты <br>задолженностей</a>
                    </li>
                </ul>
            </li>
            <li><a href="../../content/orders.php"
                   class=<?php echo($curPage === 'orders' ? '"active" disabled' : '') ?>>Продажи</a>
            </li>
            <li><a href="../../content/outgo.php" class=<?php echo($curPage === 'outgo' ? '"active" disabled' : '') ?>>Расходы</a>
            </li>
            <li><a href="../../content/referals.php"
                   class=<?php ($curPage === 'referals' ? '"active" disabled' : '') ?>>Рефералы</a></li>

            <li><a href="../../content/debts.php" class=<?php echo($curPage === 'debts' ? '"active" disabled' : '') ?>>Должники</a>
            </li>
            <li><a href="../../content/types.php"
                   class=<?php echo($curPage === 'types' ? '"active" disabled' : '') ?>>Типы расходов</a></li>
            <li><a href="../../content/statistics.php"
                   class=<?php echo($curPage === 'statistics' ? '"active" disabled' : '') ?>>Статистика</a></li>
            <li><a href="../../content/turnover.php"
                   class=<?php echo($curPage === 'turnover' ? '"active" disabled' : '') ?>>Оборот</a></li>
            <li><a href="../../content/methods-of-obtaining.php"
                   class=<?php echo($curPage === 'methods-of-obtaining' ? '"active" disabled' : '') ?>>Методы оплаты</a>
            </li>
            <li><a href="../../content/projects.php"
                   class=<?php echo($curPage === 'projects' ? '"active" disabled' : '') ?>>Проекты</a>
            </li>
            <li><a href="../../content/transfers.php"
                   class=<?php echo($curPage === 'transfers' ? '"active" disabled' : '') ?>>Переводы на счета</a>
            </li>
            <li id="exit"><a class="menu-logout-btn" href="../../api/auth/logout.php">Выйти</a></li>
        </ul>
    </div>
</nav>
<script src="../js/dropDownMenu.js"></script>
