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

$eqid = GetDef('eqid');
$coor = GetDef('coor');

$x = $coor[0][1];
$y = $coor[0][0];

$sql = 'UPDATE equipment SET mapx = :mapx, mapy = :mapy, mapmoved = 0 WHERE id = :id';
try {
	DB::prepare($sql)->execute(array(':mapx' => $x, ':mapy' => $y, ':id' => $eqid));
} catch (PDOException $ex) {
	throw new DBException('Не могу обновить координаты ТМЦ', 0, $ex);
}
