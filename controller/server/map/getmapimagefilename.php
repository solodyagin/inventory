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

$eqid = GetDef('id');

$photo = '';

$sql = 'SELECT * FROM org WHERE id = :id';
try {
	$row = DB::prepare($sql)->execute(array(':id' => $eqid))->fetch();
	if ($row) {
		$photo = $row['picmap'];
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список фото', 0, $ex);
}

echo ($photo != '') ? $photo : 'null';
