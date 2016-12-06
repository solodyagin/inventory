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

$id = GetDef('groupid', '1');
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
WHERE  groupid = :id
GROUP  BY vendorid
TXT;
try {
	$arr = DB::prepare($sql)->execute(array(':id' => $id))->fetchAll();
	foreach ($arr as $row) {
		$sl = ($row['vendorid'] == $vendorid) ? 'selected' : '';
		echo "<option value=\"{$row['vendorid']}\" $sl>{$row['name']}</option>";
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список групп', 0, $ex);
}

echo '</select>';
