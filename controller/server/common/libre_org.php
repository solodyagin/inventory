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
$oper = PostDef('oper');
$id = PostDef('id');
$name = PostDef('name');

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	$user->TestRoles('1,3,4,5,6') or die('Недостаточно прав');
	$result = $sqlcn->ExecuteSQL("SELECT COUNT(*) AS cnt FROM org");
	$row = mysqli_fetch_array($result);
	$count = $row['cnt'];
	$total_pages = ($count > 0) ? ceil($count / $limit) : 0;
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
	$sql = "SELECT id, name, active, picmap FROM org ORDER BY $sidx $sord LIMIT $start, $limit";
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать список организаций! ' . mysqli_error($sqlcn->idsqlconnection));
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
		$responce->rows[$i]['id'] = $row['id'];
		if ($row['active'] == '1') {
			$responce->rows[$i]['cell'] = array('<i class="fa fa-check-circle-o" aria-hidden="true"></i>', $row['id'], $row['name']);
		} else {
			$responce->rows[$i]['cell'] = array('<i class="fa fa-ban" aria-hidden="true"></i>', $row['id'], $row['name']);
		}
		$i++;
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Проверяем может ли пользователь добавлять?
	$user->TestRoles('1,4') or die('Недостаточно прав');
	$sql = "INSERT INTO org (id, name, active) VALUES (null, '$name', 1)";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу добавить организацию! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'edit') {
	// Проверяем может ли пользователь редактировать?
	$user->TestRoles('1,5') or die('Для редактирования не хватает прав!');
	$sql = "UPDATE org SET name='$name' WHERE id='$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу обновить данные по организации! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'del') {
	$user->TestRoles('1,6') or die('Для удаления не хватает прав!');
	$sql = "UPDATE org SET active = NOT active WHERE id = '$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу удалить организацию! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}
