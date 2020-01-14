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

$groupid = GetDef('groupid', '1');
$vendorid = GetDef('vendorid', '1');
$nomeid = GetDef('nomeid');

echo '<select class="chosen-select" name="snomeid" id="snomeid">';

$sql = 'SELECT id, name FROM nome WHERE groupid = :groupid AND vendorid = :vendorid';
try {
	$arr = DB::prepare($sql)->execute(array(
				':groupid' => $groupid,
				':vendorid' => $vendorid
			))->fetchAll();
	foreach ($arr as $row) {
		$rid = $row['id'];
		$rname = $row['name'];
		$sl = ($rid == $nomeid) ? 'selected' : '';
		echo "<option value=\"$rid\" $sl>$rname</option>";
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список номенклатуры', 0, $ex);
}

echo '</select>';
