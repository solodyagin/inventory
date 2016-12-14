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
	echo '<select name="sgroupname" id="sgroupname">';
	if ($addnone == 'true') {
		echo '<option value="-1">не выбрано</option>';
	}
	$sql = 'SELECT * FROM group_nome WHERE active = 1 ORDER BY BINARY(name)';
	try {
		$arr = DB::prepare($sql)->execute()->fetchAll();
		foreach ($arr as $row) {
			echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список групп!', 0, $ex);
	}
	echo '</select>';
} else {
	echo 'Недостаточно прав';
}
