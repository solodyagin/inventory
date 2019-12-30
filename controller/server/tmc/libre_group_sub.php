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

$page = GetDef('page', 1);
if ($page == 0) {
	$page = 1;
}
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$oper = PostDef('oper');
$groupid = GetDef('groupid');
if ($groupid == '') {
	$groupid = PostDef('groupid');
}
$id = PostDef('id');
$name = PostDef('name');

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	(($user->mode == 1) || $user->TestRoles('1,3,4,5,6')) or die('Недостаточно прав');

	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$sql = 'SELECT COUNT(*) AS cnt FROM group_param';
	try {
		$row = DB::prepare($sql)->execute()->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список параметров групп (1)', 0, $ex);
	}
	if ($count == 0) {
		jsonExit($responce);
	}

	$total_pages = ceil($count / $limit);
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
	if ($start < 0) {
		jsonExit($responce);
	}

	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;

	$sql = <<<TXT
SELECT id, name, active
FROM group_param
WHERE groupid = :groupid
ORDER BY $sidx $sord
LIMIT $start, $limit
TXT;
	try {
		$arr = DB::prepare($sql)->execute(array(':groupid' => $groupid))->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['id'];
			$ic = ($row['active'] == '1') ? 'fa-check-circle-o' : 'fa-ban';
			$responce->rows[$i]['cell'] = array(
				"<i class=\"fa $ic\" aria-hidden=\"true\"></i>",
				$row['id'], $row['name']
			);
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список параметров групп (2)', 0, $ex);
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Проверяем может ли пользователь добавлять?
	(($user->mode == 1) || $user->TestRoles('1,4')) or die('Недостаточно прав');

	if (($groupid == '') || ($name == '')) {
		die();
	}
	$sql = 'INSERT INTO group_param (id, groupid, name, active) VALUES (null, :groupid, :name, 1)';
	try {
		DB::prepare($sql)->execute(array(':groupid' => $groupid, ':name' => $name));
	} catch (PDOException $ex) {
		throw new DBException('Не могу добавить параметр группы', 0, $ex);
	}
	exit;
}

if ($oper == 'edit') {
	// Проверяем может ли пользователь редактировать?
	(($user->mode == 1) || $user->TestRoles('1,5')) or die('Недостаточно прав');

	$sql = 'UPDATE group_param SET name = :name WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':name' => $name, ':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не могу обновить данные по группе', 0, $ex);
	}
	exit;
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	(($user->mode == 1) || $user->TestRoles('1,6')) or die('Недостаточно прав');

	$sql = 'UPDATE group_param SET active = NOT active WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не могу пометить на удаление параметр группы', 0, $ex);
	}
	exit;
}
