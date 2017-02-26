<?php

/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$page = GetDef('page', 1);
if ($page == 0) {
	$page = 1;
}
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$orgid = GetDef('orgid');
$placesid = GetDef('placesid');
$oper = PostDef('oper');
$id = PostDef('id');
$name = PostDef('name');
$comment = PostDef('comment');

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	(($user->mode == 1) || $user->TestRoles('1,3,4,5,6')) or die('Недостаточно прав');

	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$sql = 'SELECT COUNT(*) AS cnt FROM places_users WHERE placesid = :placesid';
	try {
		$row = DB::prepare($sql)->execute(array(':placesid' => $placesid))->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список помещений/пользователей (1)', 0, $ex);
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
SELECT     places_users.id AS plid,
           placesid,
           userid,
           users_profile.fio AS name
FROM       places_users
INNER JOIN users_profile
ON         users_profile.usersid = userid
WHERE      placesid = :placesid
ORDER BY   $sidx $sord
LIMIT      $start, $limit
TXT;
	try {
		$arr = DB::prepare($sql)->execute(array(':placesid' => $placesid))->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['plid'];
			$responce->rows[$i]['cell'] = array($row['plid'], $row['name']);
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список помещений/пользователей (2)', 0, $ex);
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Проверяем может ли пользователь добавлять?
	(($user->mode == 1) || $user->TestRoles('1,4')) or die('Недостаточно прав');

	if (($placesid == '') || ($name == '')) {
		die();
	}
	$sql = 'INSERT INTO places_users (id, placesid, userid) VALUES (null, :placesid, :userid)';
	try {
		DB::prepare($sql)->execute(array(':placesid' => $placesid, ':userid' => $name));
	} catch (PDOException $ex) {
		throw new DBException('Не могу добавить помещение/пользователя', 0, $ex);
	}
	exit;
}

if ($oper == 'edit') {
	// Проверяем может ли пользователь редактировать?
	(($user->mode == 1) || $user->TestRoles('1,5')) or die('Недостаточно прав');

	$sql = 'UPDATE places_users SET userid = :userid WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':userid' => $name, ':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не могу обновить данные по помещениям/пользователям', 0, $ex);
	}
	exit;
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	(($user->mode == 1) || $user->TestRoles('1,6')) or die('Недостаточно прав');

	$sql = 'DELETE FROM places_users WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не могу удалить помещение/пользователя', 0, $ex);
	}
	exit;
}
