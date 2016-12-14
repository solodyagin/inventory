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

$orgid = $cfg->defaultorgid;
$addnone = GetDef('addnone');

if (($user->mode == 1) || $user->TestRoles('1,4,5,6')) {
	echo '<select name="tmcgo" id="tmcgo">';
	if ($addnone == 'true') {
		echo '<option value="-1">не выбрано</option>';
	}
	echo '<option value="0">На месте</option>';
	echo '<option value="1">В пути</option>';
	echo '</select>';
} else {
	echo 'Недостаточно прав';
}
