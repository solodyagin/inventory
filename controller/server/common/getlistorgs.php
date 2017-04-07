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

$orgid = $cfg->defaultorgid;
$addnone = GetDef('addnone');

if ($user->mode == 1) {
	echo '<select name="sogrsname" id="sorgsname">';
	if ($addnone == 'true') {
		echo '<option value="-1">не выбрано</option>';
	}
	$sql = "SELECT * FROM org WHERE active = 1 ORDER BY BINARY(name)";
	try {
		$arr = DB::prepare($sql)->execute()->fetchAll();
		foreach ($arr as $row) {
			echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список организаций', 0, $ex);
	}
	echo '</select>';
} else {
	echo 'Не достаточно прав!!!';
}
