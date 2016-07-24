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

$page = GetDef('page', '1');
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$orgid = GetDef('orgid');
$oper = PostDef('oper');
$id = PostDef('id');
$name = PostDef('name');
$comment = PostDef('comment');
$opgroup = PostDef('opgroup');

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	$user->TestRoles('1,3,4,5,6') or die('Недостаточно прав');
	$result = $sqlcn->ExecuteSQL("SELECT COUNT(*) AS cnt FROM places WHERE orgid = '$orgid'");
	$row = mysqli_fetch_array($result);
	$count = $row['cnt'];
	$total_pages = ($count > 0) ? ceil($count / $limit) : 0;
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
	$sql = "SELECT id, opgroup, name, comment, active FROM places WHERE orgid = '$orgid' ORDER BY $sidx $sord LIMIT $start, $limit";
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать список помещений! ' . mysqli_error($sqlcn->idsqlconnection));
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
		$responce->rows[$i]['id'] = $row['id'];
		if ($row['active'] == '1') {
			$responce->rows[$i]['cell'] = array('<i class="fa fa-check-circle-o" aria-hidden="true"></i>', $row['id'], $row['opgroup'], $row['name'], $row['comment']);
		} else {
			$responce->rows[$i]['cell'] = array('<i class="fa fa-ban" aria-hidden="true"></i>', $row['id'], $row['opgroup'], $row['name'], $row['comment']);
		}
		$i++;
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Проверяем может ли пользователь добавлять?
	$user->TestRoles('1,4') or die('Недостаточно прав');
	$sql = "INSERT INTO places (id, orgid, opgroup, name, comment, active) VALUES (null, '$orgid', '$opgroup', '$name', '$comment', 1)";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу добавить помещение! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'edit') {
	// Проверяем может ли пользователь редактировать?
	$user->TestRoles('1,5') or die('Недостаточно прав');
	$sql = "UPDATE places SET opgroup = '$opgroup', name = '$name', comment = '$comment' WHERE id = '$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу обновить данные по помещениям! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	$user->TestRoles('1,6') or die('Недостаточно прав');
	$sql = "UPDATE places SET active = not active WHERE id = '$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу обновить данные по помещению! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}
