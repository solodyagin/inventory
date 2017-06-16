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

$eqid = GetDef('eqid');

$photo = '';
$sql = 'SELECT * FROM org WHERE id = :eqid';
try {
	$row = DB::prepare($sql)->execute(array(':eqid' => $eqid))->fetch();
	if ($row) {
		$photo = $row['picmap'];
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список фото', 0, $ex);
}
if ($photo != '') {
	echo '<img src="photos/maps/0-0-0-' . $photo . '" width="100%">';
} else {
	echo '<img src="images/noimage.jpg" width="100%">';
}
