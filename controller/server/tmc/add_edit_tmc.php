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

if (count($err) == 0) {
	if ($step == 'edit') {
		$sql = 'UPDATE nome SET groupid = :groupid, vendorid = :vendorid, name = :name WHERE id = :id';
		try {
			DB::prepare($sql)->execute(array(
				':groupid' => $groupid,
				':vendorid' => $vendorid,
				':name' => $namenome,
				':id' => $id
			));
		} catch (PDOException $ex) {
			throw new DBException('Не смог обновить номенклатуру', 0, $ex);
		}
	}
	if ($step == 'add') {
		$sql = <<<TXT
INSERT INTO nome (id, groupid, vendorid, name, active)
VALUES (NULL, :groupid, :vendorid, :name, '1')
TXT;
		try {
			DB::prepare($sql)->execute(array(
				':groupid' => $groupid,
				':vendorid' => $vendorid,
				':name' => $namenome
			));
		} catch (PDOException $ex) {
			throw new DBException('Не смог добавить номенклатуру', 0, $ex);
		}
	}
}
echo 'ok';
