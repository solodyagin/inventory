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

/* Запрещаем прямой вызов скрипта. */
defined('SITE_EXEC') or die('Доступ запрещён');

$step = GetDef('step');
$id = GetDef('id');
$name = PostDef('name');
$comment = PostDef('comment');
$groupid = PostDef('groupid');
if ($groupid == '') {
	$err[] = 'Не выбрана группа!';
}
$vendorid = PostDef('vendorid');
if ($vendorid == '') {
	$err[] = 'Не задан производитель!';
}
$namenome = PostDef('namenome');
if ($namenome == '') {
	$err[] = 'Не задано наименование!';
}

/* Есть ли уже такая запись? */
$sql = 'SELECT COUNT(*) cnt FROM nome WHERE name = :name';
try {
	$row = DB::prepare($sql)->execute([':name' => $namenome])->fetch();
	$count = ($row) ? $row['cnt'] : 0;
} catch (PDOException $ex) {
	throw new DBException('Не смог добавить номенклатуру (1)', 0, $ex);
}
if ($count > 0) {
	$err[] = 'Запись уже существует!';
}

if (count($err) == 0) {
	if ($step == 'edit') {
		$sql = 'UPDATE nome SET groupid = :groupid, vendorid = :vendorid, name = :name WHERE id = :id';
		try {
			DB::prepare($sql)->execute([
				':groupid' => $groupid,
				':vendorid' => $vendorid,
				':name' => $namenome,
				':id' => $id
			]);
		} catch (PDOException $ex) {
			throw new DBException('Не смог обновить номенклатуру', 0, $ex);
		}
	}
	if ($step == 'add') {
		$sql = <<<TXT
INSERT INTO nome (groupid, vendorid, name, active)
VALUES (:groupid, :vendorid, :name, '1')
TXT;
		try {
			DB::prepare($sql)->execute([
				':groupid' => $groupid,
				':vendorid' => $vendorid,
				':name' => $namenome
			]);
		} catch (PDOException $ex) {
			throw new DBException('Не смог добавить номенклатуру (2)', 0, $ex);
		}
	}
}
echo (count($err) == 0) ? 'ok' : implode('<br>', $err);