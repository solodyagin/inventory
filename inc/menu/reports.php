<?php

/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчик: Грибов Павел
 * Сайт: http://грибовы.рф
 */
/*
 * Inventory - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчик: Сергей Солодягин (solodyagin@gmail.com)
 */

// Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

$this->add('main', '<i class="fas fa-chart-bar"></i> Отчёты', 'Отчёты', 3, 'reports', '');
$this->add('reports', '<i class="fab fa-empire"></i> Имущество', 'Отчеты по имуществу', 3, 'reports/report', 'report');
