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

$id = GetDef('eqid');
if ($id == '') {
	$id = PostDef('eqid');
}
$oper = PostDef('oper');
$param = PostDef('param');
$paramid = PostDef('id');

if ($oper == '') {
	$responce = new stdClass();

	// получаем группу номенклатуры
	$sql = <<<TXT
SELECT equipment.id,nome.id AS nomeid,nome.groupid AS groupid
FROM   equipment
       INNER JOIN nome
               ON nome.id = equipment.nomeid
WHERE  ( equipment.id = :id )
       AND ( nome.active = 1 )
TXT;

	try {
		$row = DB::prepare($sql)->execute(array(':id' => $id))->fetch();
		$groupid = ($row) ? $row['groupid'] : '';
	} catch (PDOException $ex) {
		throw new DBException('Не получилось найти группу', 0, $ex);
	}

	if ($groupid == '') {
		die('Нет параметров у группы!');
	}

	// получаем список параметров группы
	$sql = 'SELECT id, name FROM group_param WHERE (groupid = :groupid) AND (active = 1)';
	try {
		$arr = DB::prepare($sql)->execute(array(':groupid' => $groupid))->fetchAll();
		foreach ($arr as $row) {
			$paramid = $row['id'];
			$name = $row['name'];

			// проверяем, если какого-то параметра нет, то добавляем его в основную таблице связанную с оргтехникой
			$sql = 'SELECT id FROM eq_param WHERE (grpid = :grpid) AND (eqid = :eqid) AND (paramid = :paramid)';
			try {
				$arr2 = DB::prepare($sql)->execute(array(':grpid' => $groupid, ':eqid' => $id, ':paramid' => $paramid))->fetchAll();
				$cnt = count($arr2);
			} catch (PDOException $ex) {
				throw new DBException('Не получилось выбрать существующие параметры', 0, $ex);
			}

			// если параметра нет, то добавляем...
			if ($cnt == 0) {
				$sql = "INSERT INTO eq_param (id, grpid, paramid, eqid, param) VALUES (NULL, :grpid, :paramid, :eqid, '')";
				try {
					DB::prepare($sql)->execute(array(
						':grpid' => $groupid,
						':paramid' => $paramid,
						':eqid' => $id
					));
				} catch (PDOException $ex) {
					throw new DBException('Не смог добавить параметр', 0, $ex);
				}
			}
		}
	} catch (PDOException $ex) {
		throw new DBException('Не получилось найти параметры', 0, $ex);
	}

	// получаем список параметров конкретной позиции
	$sql = <<<TXT
SELECT eq_param.id AS pid,group_param.name AS pname,eq_param.param AS pparam
FROM   eq_param
       INNER JOIN group_param
               ON group_param.id = eq_param.paramid
WHERE  ( eqid = :eqid )
TXT;
	try {
		$arr = DB::prepare($sql)->execute(array(':eqid' => $id))->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['pid'];
			$responce->rows[$i]['cell'] = array($row['pid'], $row['pname'], $row['pparam']);
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не получилось найти параметры', 0, $ex);
	}

	jsonExit($responce);
}

if ($oper == 'edit') {
	$sql = 'UPDATE eq_param SET eq_param.param = :param WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':param' => $param, ':id' => $paramid));
	} catch (PDOException $ex) {
		throw new DBException('Не смог изменить параметр', 0, $ex);
	}
	exit;
}

if ($oper == 'del') {
	$sql = 'DELETE FROM eq_param WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':id' => $paramid));
	} catch (PDOException $ex) {
		throw new DBException('Не смог удалить параметр', 0, $ex);
	}
	exit;
}
