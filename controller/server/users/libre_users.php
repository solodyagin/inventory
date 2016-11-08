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
		if ($field == 'org.id') {
			$field = 'org.name';
		}
		$data = $flt['rules'][$i]['data'];
		$where .= "($field LIKE '%$data%')";
		if ($i < ($cnt - 1)) {
			$where .= ' AND ';
		}
	}
	if ($where != '') {
		$where = 'WHERE ' . $where;
	}
	$sql = <<<TXT
SELECT     COUNT(*) AS cnt,
           org.id   AS orgid,
           users.id,
           users.orgid,
           users.login,
           users.password,
           users.email,
           users.mode,
           users.active,
           org.name AS orgname
FROM       users
INNER JOIN org
ON         users.orgid = org.id
$where
TXT;
	$result = $sqlcn->ExecuteSQL($sql);
	$row = mysqli_fetch_array($result);
	$count = $row['cnt'];
	$total_pages = ($count > 0) ? ceil($count / $limit) : 0;
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
	$sql = <<<TXT
SELECT     org.id AS orgid,
           users.id,
           users.orgid,
           users.login,
           users.password,
           users.email,
           users.mode,
           users.active,
           org.name AS orgname
FROM       users
INNER JOIN org
ON         users.orgid = org.id
$where
ORDER BY   $sidx $sord
LIMIT      $start, $limit
TXT;
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать список пользователей! ' . mysqli_error($sqlcn->idsqlconnection));
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
		$responce->rows[$i]['id'] = $row['id'];
		$mode = ($row['mode'] == '1') ? 'Да' : 'Нет';
		if ($row['active'] == '1') {
			$responce->rows[$i]['cell'] = array(
				'<i class="fa fa-check-circle" aria-hidden="true"></i>',
				$row['id'], $row['orgname'], $row['login'], 'скрыто', $row['email'], $mode
			);
		} else {
			$responce->rows[$i]['cell'] = array(
				'<i class="fa fa-ban" aria-hidden="true"></i>',
				$row['id'], $row['orgname'], $row['login'], 'скрыто', $row['email'], $mode
			);
		}
		$i++;
	}
	jsonExit($responce);
}

if ($oper == 'edit') {
	// Только с полными правами можно редактировать пользователя!
	(($user->mode == 1) || $user->TestRoles('1')) or die('Недостаточно прав');
	$imode = ($mode == 'Да') ? '1' : '0';
	$ps = ($pass != 'скрыто') ? "`password`=SHA1(CONCAT(SHA1('$pass'), salt))," : '';
	$sql = "UPDATE users SET mode = '$imode', login = '$login', $ps email = '$email' WHERE id = '$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу обновить данные по пользователю! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'add') {
	// Только с полными правами можно добавлять пользователя!
	(($user->mode == 1) || $user->TestRoles('1')) or die('Недостаточно прав');
	$sql = "INSERT INTO knt (id, name, comment, active) VALUES (null, '$name', '$comment', 1)";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу добавить пользователя! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'del') {
	// Только с полными правами можно удалять пользователя!
	(($user->mode == 1) || $user->TestRoles('1')) or die('Недостаточно прав');
	$sql = "UPDATE users SET active = NOT active WHERE id = '$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу обновить данные по пользователю! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}
