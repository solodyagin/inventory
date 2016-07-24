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

$id = GetDef('groupid', '1');
$vid = GetDef('vendorid', '1');
$nomeid = GetDef('nomeid');

$sql = "SELECT id, name FROM nome WHERE groupid = '$id' AND vendorid = '$vid'";
$result = $sqlcn->ExecuteSQL($sql)
		or die('Не могу выбрать список номенклатуры! ' . mysqli_error($sqlcn->idsqlconnection));
echo '<select class="chosen-select" name="snomeid" id="snomeid">';
while ($row = mysqli_fetch_array($result)) {
	$sl = ($row['id'] == $nomeid) ? 'selected' : '';
	echo "<option value=\"{$row['id']}\" $sl>{$row['name']}</option>";
}
echo '</select>';
