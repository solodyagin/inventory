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
	$sql = 'SELECT * FROM group_nome WHERE active = 1 ORDER BY BINARY(name)';
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать список групп!' . mysqli_error($sqlcn->idsqlconnection));
	echo '<select name="sgroupname" id="sgroupname">';
	if ($addnone == 'true') {
		echo '<option value="-1" >нет выбора</option>';
	}
	while ($row = mysqli_fetch_array($result)) {
		echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
	}
	echo '</select>';
} else {
	echo 'Не достаточно прав!!!';
}
