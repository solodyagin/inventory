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
//use stdClass;
use core\db;
use core\dbexception;
use core\request;
use core\utils;

$req = request::getInstance();
$id = $req->get('eqid');
if ($id == '') {
	$id = $req->get('eqid');
}
$oper = $req->get('oper');
$param = $req->get('pparam');
$paramid = $req->get('id');

if ($oper == '') {
	$responce = new stdClass();
	// Получаем группу номенклатуры
	try {
		$sql = <<<TXT
select
	equipment.id,
	nome.id as nomeid,
	nome.groupid as groupid
from equipment
	inner join nome on nome.id = equipment.nomeid
where equipment.id = :id
	and nome.active = 1
TXT;
		$row = db::prepare($sql)->execute([':id' => $id])->fetch();
		$groupid = ($row) ? $row['groupid'] : '';
	} catch (PDOException $ex) {
		throw new dbexception('Не получилось найти группу', 0, $ex);
	}
	if ($groupid == '') {
		die('Нет параметров у группы!');
	}

	// Получаем список параметров группы
	try {
		$sql = 'select id, name from group_param where groupid = :groupid and active = 1';
		$arr = db::prepare($sql)->execute([':groupid' => $groupid])->fetchAll();
		foreach ($arr as $row) {
			$paramid = $row['id'];
			$name = $row['name'];
			// Проверяем, если какого-то параметра нет, то добавляем его в основную таблицу, связанную с оргтехникой
			$sql = 'select id from eq_param where grpid = :grpid and eqid = :eqid and paramid = :paramid';
			try {
				$arr2 = db::prepare($sql)->execute([':grpid' => $groupid, ':eqid' => $id, ':paramid' => $paramid])->fetchAll();
				$cnt = count($arr2);
			} catch (PDOException $ex) {
				throw new dbexception('Не получилось выбрать существующие параметры', 0, $ex);
			}
			// Если параметра нет, то добавляем...
			if ($cnt == 0) {
				$sql = "insert into eq_param (grpid, paramid, eqid, param) values (:grpid, :paramid, :eqid, '')";
				try {
					db::prepare($sql)->execute([':grpid' => $groupid, ':paramid' => $paramid, ':eqid' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не смог добавить параметр', 0, $ex);
				}
			}
		}
	} catch (PDOException $ex) {
		throw new dbexception('Не получилось найти параметры', 0, $ex);
	}

	// Получаем список параметров конкретной позиции
	try {
		$sql = <<<TXT
select
	eq_param.id as pid,
	group_param.name as pname,
	eq_param.param as pparam
from eq_param
	inner join group_param on group_param.id = eq_param.paramid
where eqid = :eqid
TXT;
		$rows = db::prepare($sql)->execute([':eqid' => $id])->fetchAll();
		$i = 0;
		foreach ($rows as $row) {
			$responce->rows[$i]['id'] = $row['pid'];
			$responce->rows[$i]['cell'] = [$row['pid'], $row['pname'], $row['pparam']];
			$i++;
		}
	} catch (PDOException $ex) {
		throw new dbexception('Не получилось найти параметры', 0, $ex);
	}
	utils::jsonExit($responce);
}

if ($oper == 'edit') {
	try {
		$sql = 'update eq_param set param = :param where id = :id';
		db::prepare($sql)->execute([':param' => $param, ':id' => $paramid]);
	} catch (PDOException $ex) {
		throw new dbexception('Не смог изменить параметр', 0, $ex);
	}
	exit;
}

if ($oper == 'del') {
	try {
		$sql = 'delete from eq_param where id = :id';
		db::prepare($sql)->execute([':id' => $paramid]);
	} catch (PDOException $ex) {
		throw new dbexception('Не смог удалить параметр', 0, $ex);
	}
	exit;
}
