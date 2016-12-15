<?php

/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$this->Add('main', '<i class="fa fa-bar-chart fa-fw"> </i>Отчёты', 'Отчёты', 3, 'reports', '');
$this->Add('reports', '<i class="fa fa-map fa-fw"> </i>Размещение ТМЦ на карте', 'Размещение ТМЦ на карте', 3, 'reports/map', 'map');
$this->Add('reports', '<i class="fa fa-empire fa-fw"> </i>Имущество', 'Отчеты по имуществу', 3, 'reports/report_tmc', 'report_tmc');
