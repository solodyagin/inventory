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
$placesid = GetDef('placesid');
$addnone = GetDef('addnone');
$oldopgroup = '';

if (($user->mode == 1) || $user->TestRoles('1,4,5,6')) {
	echo '<select name="splaces" id="splaces">';
	if ($addnone == 'true') {
		echo '<option value="-1">не выбрано</option>';
	}
	$sql = 'SELECT * FROM places WHERE orgid = :orgid AND active = 1 ORDER BY BINARY(opgroup), BINARY(name)';
	try {
		$arr = DB::prepare($sql)->execute(array(':orgid' => $orgid))->fetchAll();
		$flag = 0;
		foreach ($arr as $row) {
			$opgroup = $row['opgroup'];
			if ($opgroup != $oldopgroup) {
				if ($flag != 0) {
					echo '</optgroup>';
				}
				echo "<optgroup label=\"$opgroup\">";
				$flag = 1;
			}
			$sl = ($row['id'] == $placesid) ? 'selected' : '';
			echo "<option value=\"{$row['id']}\" $sl>{$row['name']}</option>";
			$oldopgroup = $opgroup;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список помещений!', 0, $ex);
	}
	echo '</optgroup>';
	echo '</select>';
} else {
	echo 'Недостаточно прав';
}
