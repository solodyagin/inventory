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

/*
 * Роли:
 * http://грибовы.рф/wiki/doku.php/основы:доступ:роли
 * 1 = 'Полный доступ'
 * 2 = 'Просмотр финансовых отчетов'
 * 3 = 'Просмотр'
 * 4 = 'Добавление'
 * 5 = 'Редактирование'
 * 6 = 'Удаление'
 * 7 = 'Отправка СМС'
 * 8 = 'Манипуляции с деньгами'
 * 9 = 'Редактирование карт'
 */

$page = GetDef('page', '1');
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$id = PostDef('id');
$role = PostDef('role');
$userid = GetDef('userid');
$oper = PostDef('oper');

// Роли
$roles = array(
	'1' => 'Полный доступ',
	'2' => 'Просмотр финансовых отчетов',
	'3' => 'Просмотр',
	'4' => 'Добавление',
	'5' => 'Редактирование',
	'6' => 'Удаление',
	'7' => 'Отправка СМС',
	'8' => 'Манипуляции с деньгами',
	'9' => 'Редактирование карт'
);

if ($oper == '') {
	// Разрешаем при наличии ролей "Полный доступ" и "Просмотр"
	$user->TestRoles('1,3') or die('Недостаточно прав');
	$result = $sqlcn->ExecuteSQL("SELECT COUNT(*) AS cnt FROM usersroles WHERE userid = '$userid'");
	$row = mysqli_fetch_array($result);
	$count = $row['cnt'];
	$total_pages = ($count > 0) ? ceil($count / $limit) : 0;
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
	$sql = "SELECT * FROM usersroles WHERE userid = '$userid' ORDER BY $sidx $sord LIMIT $start, $limit";
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать список ролей пользователей! ' . mysqli_error($sqlcn->idsqlconnection));
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
		$responce->rows[$i]['id'] = $row['id'];
		$role = $roles[$row['role']];
		$responce->rows[$i]['cell'] = array($row['id'], $role);
		$i++;
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Только с полными правами можно добавлять роль!
	$user->TestRoles('1') or die('Недостаточно прав');
	$sql = "INSERT INTO usersroles (userid, role) VALUES ('$userid', '$role')";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу добавить роль пользователя!' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'del') {
	// Только с полными правами можно удалять роль!
	$user->TestRoles('1') or die('Недостаточно прав');
	$sql = "DELETE FROM usersroles WHERE id = '$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу удалить роль пользователя!' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}
