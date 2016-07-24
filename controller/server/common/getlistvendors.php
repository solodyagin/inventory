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
$vendorid = GetDef('vendorid', '1');
$addnone = GetDef('addnone');

$sql = <<<TXT
SELECT vendorid,vendor.name,COUNT(nome.id)
FROM   nome
       INNER JOIN vendor
               ON vendor.id = vendorid
WHERE  groupid = '$id'
GROUP  BY vendorid
TXT;
$result = $sqlcn->ExecuteSQL($sql)
		or die('Не могу выбрать список групп! ' . mysqli_error($sqlcn->idsqlconnection));
echo '<select class="chosen-select" name="svendid" id="svendid">';
if ($addnone == 'true') {
	echo '<option value="-1">нет выбора</option>';
}
while ($row = mysqli_fetch_array($result)) {
	$sl = ($row['vendorid'] == $vendorid) ? 'selected' : '';
	echo "<option value=\"{$row['vendorid']}\" $sl>{$row['name']}</option>";
}
echo '</select>';
