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

$page = GetDef('page', 1);
if ($page == 0) {
	$page = 1;
}
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$oper = PostDef('oper');
$id = PostDef('id');
$name = PostDef('name');
$comment = PostDef('comment');

$user = User::getInstance();

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	($user->isAdmin() || $user->TestRoles('1,3,4,5,6')) or die('Недостаточно прав');

	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$sql = 'SELECT COUNT(*) AS cnt FROM vendor';
	try {
		$row = DB::prepare($sql)->execute()->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список производителей (1)', 0, $ex);
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

	$sql = "SELECT id, name, comment, active FROM vendor ORDER BY $sidx $sord LIMIT $start, $limit";
	try {
		$arr = DB::prepare($sql)->execute()->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['id'];
			$ic = ($row['active'] == '1') ? 'fa-check-circle-o' : 'fa-ban';
			$responce->rows[$i]['cell'] = array(
				"<i class=\"fa $ic\" aria-hidden=\"true\"></i>",
				$row['id'], $row['name'], $row['comment']
			);
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список производителей (2)', 0, $ex);
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Проверяем может ли пользователь добавлять?
	($user->isAdmin() || $user->TestRoles('1,4')) or die('Недостаточно прав');

	$sql = 'INSERT INTO vendor (id, name, comment, active) VALUES (null, :name, :comment, 1)';
	try {
		DB::prepare($sql)->execute(array(
			':name' => $name,
			':comment' => $comment
		));
	} catch (PDOException $ex) {
		throw new DBException('Не могу добавить производителя', 0, $ex);
	}
	exit;
}

if ($oper == 'edit') {
	// Проверяем может ли пользователь редактировать?
	($user->isAdmin() || $user->TestRoles('1,5')) or die('Недостаточно прав');

	$sql = 'UPDATE vendor SET name = :name, comment = :comment WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(
			':name' => $name,
			':comment' => $comment,
			':id' => $id
		));
	} catch (PDOException $ex) {
		throw new DBException('Не могу обновить данные по производителю', 0, $ex);
	}
	exit;
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	($user->isAdmin() || $user->TestRoles('1,6')) or die('Недостаточно прав');

	$sql = 'UPDATE vendor SET active = NOT active WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не могу пометить на удаление производителя', 0, $ex);
	}
	exit;
}
