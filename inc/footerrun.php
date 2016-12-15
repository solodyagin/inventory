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

// запускаем поочередно все скрипты из каталога /footerrun
$mfiles = GetArrayFilesInDir(WUO_ROOT . '/footerrun');
foreach ($mfiles as $fname) {
	include_once(WUO_ROOT . "/footerrun/$fname");
}
