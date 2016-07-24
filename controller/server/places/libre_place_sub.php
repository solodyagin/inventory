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
$placesid = GetDef('placesid');
$oper = PostDef('oper');
$id = PostDef('id');
$name = PostDef('name');
$comment = PostDef('comment');

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	$user->TestRoles('1,3,4,5,6') or die('Недостаточно прав');
	$result = $sqlcn->ExecuteSQL("SELECT COUNT(*) AS cnt FROM places_users WHERE placesid = '$placesid'");
	$row = mysqli_fetch_array($result);
	$count = $row['cnt'];
	$total_pages = ($count > 0) ? ceil($count / $limit) : 0;
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
	$sql = <<<TXT
SELECT     places_users.id AS plid,
           placesid,
           userid,
           users_profile.fio AS name
FROM       places_users
INNER JOIN users_profile
ON         users_profile.usersid = userid
WHERE      placesid = '$placesid'
ORDER BY   $sidx $sord
LIMIT      $start, $limit
TXT;
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать список помещений/пользователей! ' . mysqli_error($sqlcn->idsqlconnection));
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
		$responce->rows[$i]['id'] = $row['plid'];
		$responce->rows[$i]['cell'] = array($row['plid'], $row['name']);
		$i++;
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Проверяем может ли пользователь добавлять?
	$user->TestRoles('1,4') or die('Недостаточно прав');
	if (($placesid == '') or ( $name == '')) {
		die();
	}
	$sql = "INSERT INTO places_users (id, placesid, userid) VALUES (null, '$placesid', '$name')";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу добавить помещение/пользователя! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'edit') {
	// Проверяем может ли пользователь редактировать?
	$user->TestRoles('1,5') or die('Недостаточно прав');
	$sql = "UPDATE places_users SET userid = '$name' WHERE id = '$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу обновить данные по помещениям/пользователям! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	$user->TestRoles('1,6') or die('Недостаточно прав');
	$sql = "DELETE FROM places_users WHERE id = '$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу удалить помещение/пользователя! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}
