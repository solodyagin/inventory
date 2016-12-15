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

/*
 * Запускаем поочередно все скрипты из каталога /autorun
 */
$mfiles = GetArrayFilesInDir(WUO_ROOT . '/autorun');
foreach ($mfiles as &$fname) {
	include_once(WUO_ROOT . "/autorun/$fname");
}
unset($fname);
