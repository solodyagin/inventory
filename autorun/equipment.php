<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$cfg->quickmenu[] = '<div><i class="fa fa-shopping-basket fa-fw"></i> <a href="equipment">Имущество</a></div>';
$cfg->quickmenu[] = '<div><i class="fa fa-tasks fa-fw"></i> <a href="eqlist">ТМЦ на моём рабочем месте</a></div>';
