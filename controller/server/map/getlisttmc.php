<?php
/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$placesid = GetDef('placesid');
$addnone = GetDef('addnone');

echo '<select name="splacestmc" id="splacestmc" size="10">';
if ($addnone == 'true') {
	echo '<option value="-1">не выбрано</option>';
}

$sql = <<<TXT
SELECT nome.id AS nid,nome.name AS nomename,eq.id AS eqid
FROM   nome
       INNER JOIN (SELECT nomeid,id
                   FROM   equipment
                   WHERE  placesid = :placesid) AS eq
               ON nome.id = eq.nomeid
TXT;

try {
	$arr = DB::prepare($sql)->execute(array(':placesid' => $placesid))->fetchAll();
	foreach ($arr as $row) {
		$sl = ($row['eqid'] == $placesid) ? 'selected' : '';
		echo "<option value=\"{$row[eqid]}\" $sl>{$row['nomename']}</option>";
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список помещений', 0, $ex);
}

echo '</select>';
?>
