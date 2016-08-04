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

$orgid = GetDef('orgid', '1');
$placesid = GetDef('placesid', '1');
$addnone = GetDef('addnone');

$sql = "SELECT * FROM places WHERE orgid = '$orgid' AND active = 1 ORDER BY name";
$result = $sqlcn->ExecuteSQL($sql)
		or die('Не могу выбрать список помещений! ' . mysqli_error($sqlcn->idsqlconnection));
echo '<select class="chosen-select" name="splaces" id="splaces">';
if ($addnone == 'true') {
	echo '<option value="-1">не выбрано</option>';
}
while ($row = mysqli_fetch_array($result)) {
	$sl = ($row['id'] == $placesid) ? 'selected' : '';
	echo "<option value=\"{$row['id']}\" $sl>{$row['name']}</option>";
}
echo '</select>';
