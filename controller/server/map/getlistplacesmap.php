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

$orgid = GetDef('orgid');
$placesid = GetDef('placesid');
$addnone = GetDef('addnone');

echo '<select name="splaces" id="splaces">';
if ($addnone == 'true') {
	echo '<option value="-1">не выбрано</option>';
}

$sql = 'SELECT * FROM places WHERE orgid = :orgid AND active = 1 ORDER BY name';
try {
	$arr = DB::prepare($sql)->execute(array(':orgid' => $orgid))->fetchAll();
	foreach ($arr as $row) {
		$vl = $row['id'];
		$sl = ($vl == $placesid) ? 'selected' : '';
		echo "<option value=\"$vl\" $sl>{$row['name']}</option>";
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список помещений', 0, $ex);
}

echo '</select>';
?>
<script src="controller/client/js/mapsplaces.js"></script>
