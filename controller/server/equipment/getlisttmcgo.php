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

$orgid = $cfg->defaultorgid;
$addnone = GetDef('addnone');

if ($user->TestRoles('1,4,5,6')) {
	echo '<select name="tmcgo" id="tmcgo">';
	if ($addnone == 'true') {
		echo '<option value="-1">нет выбора</option>';
	}
	echo '<option value="0">На месте</option>';
	echo '<option value="1">В пути</option>';
	echo '</select>';
} else {
	echo 'Не достаточно прав!!!';
}
