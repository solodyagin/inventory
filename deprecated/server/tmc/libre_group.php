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
$id = PostDef('id');
$name = PostDef('name');
$comment = PostDef('comment');

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	(($user->mode == 1) || $user->TestRights([1,3,4,5,6])) or die('Недостаточно прав');

	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$sql = 'SELECT COUNT(*) AS cnt FROM group_nome';
	try {
		$row = DB::prepare($sql)->execute()->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список групп (1)', 0, $ex);
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

	$sql = "SELECT id, name, comment, active FROM group_nome ORDER BY $sidx $sord LIMIT $start, $limit";
	try {
		$arr = DB::prepare($sql)->execute(array())->fetchAll();
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
		throw new DBException('Не могу выбрать список групп (2)', 0, $ex);
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Проверяем может ли пользователь добавлять?
	(($user->mode == 1) || $user->TestRights([1,4])) or die('Недостаточно прав');

	$sql = 'INSERT INTO group_nome (id, name, comment, active) VALUES (null, :name, :comment, 1)';
	try {
		DB::prepare($sql)->execute(array(
			':name' => $name,
			':comment' => $comment
		));
	} catch (PDOException $ex) {
		throw new DBException('Не могу добавить группу', 0, $ex);
	}
	exit;
}

if ($oper == 'edit') {
	// Проверяем может ли пользователь редактировать?
	(($user->mode == 1) || $user->TestRights([1,5])) or die('Недостаточно прав');

	$sql = 'UPDATE group_nome SET name = :name, comment = :comment WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(
			':name' => $name,
			':comment' => $comment,
			':id' => $id
		));
	} catch (PDOException $ex) {
		throw new DBException('Не могу обновить данные по группе', 0, $ex);
	}
	exit;
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	(($user->mode == 1) || $user->TestRights([1,6])) or die('Недостаточно прав');

	$sql = 'UPDATE group_nome SET active = NOT active WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не могу пометить на удаление группу', 0, $ex);
	}

	$sql = <<<TXT
UPDATE group_param
SET active = (
	SELECT active FROM group_nome WHERE id = :id
)
WHERE groupid = :id
TXT;
	try {
		DB::prepare($sql)->execute(array(
			':active' => $active,
			':id' => $id
		));
	} catch (PDOException $ex) {
		throw new DBException('Не могу обновить данные по группе', 0, $ex);
	}
	exit;
}
