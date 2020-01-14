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

/* Запрещаем прямой вызов скрипта. */
defined('SITE_EXEC') or die('Доступ запрещён');

/*
 * Роли: http://грибовы.рф/wiki/doku.php/основы:доступ:роли
 */

$page = GetDef('page', 1);
if ($page == 0) {
	$page = 1;
}
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$id = PostDef('id');
$role = PostDef('role');
$userid = GetDef('userid');
$oper = PostDef('oper');

/* Роли */
$roles = [
	'1' => 'Полный доступ',
	'2' => 'Просмотр финансовых отчетов',
	'3' => 'Просмотр',
	'4' => 'Добавление',
	'5' => 'Редактирование',
	'6' => 'Удаление',
	'7' => 'Отправка СМС',
	'8' => 'Манипуляции с деньгами',
	'9' => 'Редактирование карт'
];

$user = User::getInstance();

if ($oper == '') {
	/* Разрешаем при наличии ролей "Полный доступ" и "Просмотр" */
	($user->isAdmin() || $user->TestRoles('1,3')) or die('Недостаточно прав');
	/* Готовим ответ */
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;
	$sql = 'SELECT COUNT(*) AS cnt FROM usersroles WHERE userid = :userid';
	try {
		$row = DB::prepare($sql)->execute([':userid' => $userid])->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список ролей пользователей (1)', 0, $ex);
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
SELECT * FROM usersroles
WHERE userid = :userid
ORDER BY $sidx $sord
LIMIT $start, $limit
TXT;
	try {
		$arr = DB::prepare($sql)->execute([':userid' => $userid])->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['id'];
			$role = $roles[$row['role']];
			$responce->rows[$i]['cell'] = array($row['id'], $role);
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список ролей пользователей (2)', 0, $ex);
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	/* Только с полными правами можно добавлять роль! */
	($user->isAdmin() || $user->TestRoles('1')) or die('Недостаточно прав');
	$sql = 'INSERT INTO usersroles (userid, role) VALUES (:userid, :role)';
	try {
		DB::prepare($sql)->execute([
			':userid' => $userid,
			':role' => $role
		]);
	} catch (PDOException $ex) {
		throw new DBException('Не могу добавить роль пользователя', 0, $ex);
	}
	exit;
}

if ($oper == 'del') {
	/* Только с полными правами можно удалять роль! */
	($user->isAdmin() || $user->TestRoles('1')) or die('Недостаточно прав');
	$sql = 'DELETE FROM usersroles WHERE id = :id';
	try {
		DB::prepare($sql)->execute([':id' => $id]);
	} catch (PDOException $ex) {
		throw new DBException('Не могу удалить роль пользователя', 0, $ex);
	}
	exit;
}
