<?php

/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$id = GetDef('eqid');
$oper = PostDef('oper');
$param = PostDef('param');
$paramidid = PostDef('id');
if ($id == '') {
	$id = PostDef('eqid');
}

if ($oper == '') {
	$responce = new stdClass();
	// получаем группу номенклатуры
	$sql = <<<TXT
SELECT equipment.id,nome.id AS nomeid,nome.groupid AS groupid
FROM   equipment
       INNER JOIN nome
               ON nome.id = equipment.nomeid
WHERE  ( equipment.id = '$id' )
       AND ( nome.active = 1 )
TXT;
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не получилось найти группу! ' . mysqli_error($sqlcn->idsqlconnection));
	while ($row = mysqli_fetch_array($result)) {
		$groupid = $row['groupid'];
	}
	if ($groupid == '') {
		die('Нет параметров у группы!');
	}
	// получаем список параметров группы
	$sql = "SELECT id, name FROM group_param WHERE (groupid = '$groupid') AND (active = 1)";
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не получилось найти параметры! ' . mysqli_error($sqlcn->idsqlconnection));
	while ($row = mysqli_fetch_array($result)) {
		$paramid = $row['id'];
		$name = $row['name'];
		// проверяем, если какогото параметра нет, то добавляем его в основную таблице связанную с оргнехникой
		$sql = "SELECT id FROM eq_param WHERE (grpid = '$groupid') AND (eqid = '$id') AND (paramid = '$paramid')";
		$res2 = $sqlcn->ExecuteSQL($sql)
				or die('Не получилось выбрать существующие параметры! ' . mysqli_error($sqlcn->idsqlconnection));
		$cnt = 0;
		while ($row2 = mysqli_fetch_array($res2)) {
			$cnt++;
		}
		// если параметра нет, то добавляем...
		if ($cnt == 0) {
			$sql = "INSERT INTO eq_param (id, grpid, paramid, eqid) VALUES (NULL, '$groupid', '$paramid', '$id')";
			$sqlcn->ExecuteSQL($sql)
					or die('Не смог добавить параметр!: ' . mysqli_error($sqlcn->idsqlconnection));
		}
	}

	// получаем список параметров конкретной позиции
	$sql = <<<TXT
SELECT eq_param.id AS pid,group_param.name AS pname,eq_param.param AS pparam
FROM   eq_param
       INNER JOIN group_param
               ON group_param.id = eq_param.paramid
WHERE  ( eqid = '$id' )
TXT;
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не получилось найти параметры! ' . mysqli_error($sqlcn->idsqlconnection));
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
		$responce->rows[$i]['id'] = $row['pid'];
		$responce->rows[$i]['cell'] = array(
			$row['pid'], $row['pname'], $row['pparam']
		);
		$i++;
	}
	jsonExit($responce);
}

if ($oper == 'edit') {
	$sql = "UPDATE eq_param SET eq_param.param = '$param' WHERE id = '$paramidid'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не смог изменить параметр!: ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'del') {
	$sql = "DELETE FROM eq_param WHERE id = '$paramidid'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не смог удалить параметр!: ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}
