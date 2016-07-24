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

// запускаем поочередно все скрипты из каталога /footerrun
$mfiles = GetArrayFilesInDir(WUO_ROOT . '/footerrun');
foreach ($mfiles as &$fname) {
	include_once(WUO_ROOT . "/footerrun/$fname");
}
unset($fname);
