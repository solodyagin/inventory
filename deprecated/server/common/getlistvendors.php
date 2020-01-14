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
$addnone = GetDef('addnone');

echo '<select class="chosen-select" name="svendid" id="svendid">';
if ($addnone == 'true') {
	echo '<option value="-1">не выбрано</option>';
}
$sql = <<<TXT
SELECT vendorid,vendor.name,COUNT(nome.id)
FROM   nome
       INNER JOIN vendor
               ON vendor.id = vendorid
WHERE  groupid = :groupid
GROUP  BY vendorid
TXT;
try {
	$arr = DB::prepare($sql)->execute(array(':groupid' => $groupid))->fetchAll();
	foreach ($arr as $row) {
		$sl = ($row['vendorid'] == $vendorid) ? 'selected' : '';
		echo "<option value=\"{$row['vendorid']}\" $sl>{$row['name']}</option>";
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список групп', 0, $ex);
}

echo '</select>';
