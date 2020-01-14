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

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

$orgid = $cfg->defaultorgid;
$addnone = GetDef('addnone');

if (($user->mode == 1) || $user->TestRights([1,4,5,6])) {
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
