<?php

/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
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
