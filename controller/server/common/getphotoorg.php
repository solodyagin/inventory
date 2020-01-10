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

$eqid = GetDef('eqid');

$photo = '';
$sql = 'SELECT * FROM org WHERE id = :eqid';
try {
	$row = DB::prepare($sql)->execute([':eqid' => $eqid])->fetch();
	if ($row) {
		$photo = $row['picmap'];
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список фото', 0, $ex);
}
if ($photo != '') {
	echo '<img src="photos/maps/0-0-0-' . $photo . '" width="100%">';
} else {
	echo '<img src="photos/noimage.jpg" width="100%">';
}
