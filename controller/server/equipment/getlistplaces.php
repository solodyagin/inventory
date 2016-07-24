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
$placesid = GetDef('placesid');
$addnone = GetDef('addnone');
$oldopgroup = '';
if ($user->TestRoles('1,4,5,6')) {
	$sql = "SELECT * FROM places WHERE orgid = '$orgid' AND active = 1 ORDER BY BINARY(opgroup), BINARY(name)";
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать список помещений! ' . mysqli_error($sqlcn->idsqlconnection));
	echo '<select name="splaces" id="splaces">';
	if ($addnone == 'true') {
		echo '<option value="-1">нет выбора</option>';
	}
	$flag = 0;
	while ($row = mysqli_fetch_array($result)) {
		$opgroup = $row['opgroup'];
		if ($opgroup != $oldopgroup) {
			if ($flag != 0) {
				echo '</optgroup>';
			}
			echo '<optgroup label="' . $opgroup . '">';
			$flag = 1;
		}
		$sl = ($row['id'] == $placesid) ? 'selected' : '';
		echo '<option value="' . $row['id'] . '" ' . $sl . '>' . $row['name'] . '</option>';
		$oldopgroup = $opgroup;
	}
	echo '</optgroup>';
	echo '</select>';
} else {
	echo 'Не достаточно прав!!!';
}
