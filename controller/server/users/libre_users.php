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
$filters = GetDef('filters');
$orgid = PostDef('orgid');
$oper = PostDef('oper');
$id = PostDef('id');
$login = PostDef('login');
$pass = PostDef('pass');
$email = PostDef('email');
$mode = PostDef('mode');

if ($oper == '') {
	// Разрешаем при наличии ролей "Полный доступ" и "Просмотр"
	(($user->mode == 1) || $user->TestRoles('1,3')) or die('Недостаточно прав');

	$flt = json_decode($filters, true);
	$cnt = count($flt['rules']);
	$where = '';
	for ($i = 0; $i < $cnt; $i++) {
		$field = $flt['rules'][$i]['field'];
		$data = $flt['rules'][$i]['data'];
		$where .= "($field LIKE '%$data%')";
		if ($i < ($cnt - 1)) {
			$where .= ' AND ';
		}
	}
	if ($where != '') {
		$where = 'WHERE ' . $where;
	}

	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$sql = 'SELECT COUNT(*) `cnt` FROM `users`';
	try {
		$row = DB::prepare($sql)->execute()->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список пользователей (1)', 0, $ex);
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
SELECT	u.`id`,
		o.`name` `orgname`,
		p.`fio`,
		u.`login`,
		u.`password`,
		u.`email`,
		u.`mode`,
		u.`active`
FROM	`users` u
	INNER JOIN `org` o
		ON o.`id` = u.`orgid`
	INNER JOIN `users_profile` p
		ON p.`usersid` = u.`id`
$where
ORDER BY	$sidx $sord
LIMIT		$start, $limit
TXT;
	try {
		$arr = DB::prepare($sql)->execute()->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['id'];
			$mode = ($row['mode'] == '1') ? 'Да' : 'Нет';
			$ic = ($row['active'] == '1') ? 'fa-check-circle' : 'fa-ban';
			$responce->rows[$i]['cell'] = array(
				"<i class=\"fa $ic\" aria-hidden=\"true\"></i>",
				$row['id'], $row['orgname'], $row['fio'], $row['login'], 'скрыто', $row['email'], $mode
			);
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список пользователей (2)', 0, $ex);
	}
	jsonExit($responce);
}

if ($oper == 'edit') {
	// Только с полными правами можно редактировать пользователя!
	(($user->mode == 1) || $user->TestRoles('1')) or die('Недостаточно прав');

	$imode = ($mode == 'Да') ? '1' : '0';
	$ps = ($pass != 'скрыто') ? "`password`=SHA1(CONCAT(SHA1('$pass'), salt))," : '';
	$sql = "UPDATE users SET mode = :mode, login = :login, $ps email = :email WHERE id = :id";
	try {
		DB::prepare($sql)->execute(array(
			':mode' => $imode,
			':login' => $login,
			':email' => $email,
			':id' => $id
		));
	} catch (PDOException $ex) {
		throw new DBException('Не могу обновить данные по пользователю', 0, $ex);
	}
	exit;
}
/*
if ($oper == 'add') {
	// Только с полными правами можно добавлять пользователя!
	(($user->mode == 1) || $user->TestRoles('1')) or die('Недостаточно прав');

	$sql = 'INSERT INTO knt (id, name, comment, active) VALUES (null, :name, :comment, 1)';
	try {
		DB::prepare($sql)->execute(array(
			':name' => $name,
			':comment' => $comment
		));
	} catch (PDOException $ex) {
		throw new DBException('Не могу добавить пользователя', 0, $ex);
	}
	exit;
}
*/
if ($oper == 'del') {
	// Только с полными правами можно удалять пользователя!
	(($user->mode == 1) || $user->TestRoles('1')) or die('Недостаточно прав');

	$sql = 'UPDATE users SET active = NOT active WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не могу пометить на удаление пользователя', 0, $ex);
	}
	exit;
}
