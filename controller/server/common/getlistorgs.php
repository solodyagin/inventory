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

if ($user->mode == '1') {
	$sql = "SELECT * FROM org WHERE active = 1 ORDER BY BINARY(name)";
	$result = $sqlcn->ExecuteSQL($sql)
			or die("Не могу выбрать список организаций!" . mysqli_error($sqlcn->idsqlconnection));
	echo '<select name="sogrsname" id="sorgsname">';
	if ($addnone == 'true') {
		echo '<option value="-1">не выбрано</option>';
	}
	while ($row = mysqli_fetch_array($result)) {
		echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
	}
	echo '</select>';
} else {
	echo 'Не достаточно прав!!!';
}
