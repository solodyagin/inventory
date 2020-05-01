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

// Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

//use PDOException;
use core\db;
use core\dbexception;
use core\request;

$req = request::getInstance();
$step = $req->get('step');
$id = $req->get('id');
$name = $req->get('name');
$comment = $req->get('comment');
$groupid = $req->get('groupid');
if ($groupid == '') {
	$err[] = 'Не выбрана группа!';
}
$vendorid = $req->get('vendorid');
if ($vendorid == '') {
	$err[] = 'Не задан производитель!';
}
$namenome = $req->get('namenome');
if ($namenome == '') {
	$err[] = 'Не задано наименование!';
}

// Есть ли уже такая запись?
try {
	$sql = 'select count(*) cnt from nome where name = :name';
	$row = db::prepare($sql)->execute([':name' => $namenome])->fetch();
	$count = ($row) ? $row['cnt'] : 0;
} catch (PDOException $ex) {
	throw new dbexception('Не смог добавить номенклатуру (1)', 0, $ex);
}
if ($count > 0) {
	$err[] = 'Запись уже существует!';
}
if (count($err) == 0) {
	if ($step == 'edit') {
		try {
			$sql = 'update nome set groupid = :groupid, vendorid = :vendorid, name = :name where id = :id';
			db::prepare($sql)->execute([
				':groupid' => $groupid,
				':vendorid' => $vendorid,
				':name' => $namenome,
				':id' => $id
			]);
		} catch (PDOException $ex) {
			throw new dbexception('Не смог обновить номенклатуру', 0, $ex);
		}
	}
	if ($step == 'add') {
		try {
			$sql = <<<TXT
INSERT INTO nome (groupid, vendorid, name, active)
VALUES (:groupid, :vendorid, :name, 1)
TXT;
			db::prepare($sql)->execute([
				':groupid' => $groupid,
				':vendorid' => $vendorid,
				':name' => $namenome
			]);
		} catch (PDOException $ex) {
			throw new dbexception('Не смог добавить номенклатуру (2)', 0, $ex);
		}
	}
}
echo (count($err) == 0) ? 'ok' : implode('<br>', $err);
