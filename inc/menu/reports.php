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

/* Запрещаем прямой вызов скрипта. */
defined('SITE_EXEC') or die('Доступ запрещён');

$this->Add('main', '<i class="fa fa-bar-chart fa-fw"></i> Отчёты', 'Отчёты', 3, 'reports', '');
$this->Add('reports', '<i class="fa fa-empire fa-fw"></i> Имущество', 'Отчеты по имуществу', 3, 'reports/report', 'report');
