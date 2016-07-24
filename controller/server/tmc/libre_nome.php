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
$nomename = PostDef('nomename');
$filters = GetDef('filters');

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	$user->TestRoles('1,3,4,5,6') or die('Недостаточно прав');
	$flt = json_decode($filters, true);
	$cnt = count($flt['rules']);
	$where = '';
	for ($i = 0; $i < $cnt; $i++) {
		$field = $flt['rules'][$i]['field'];
		if ($field == 'nomeid') {
			$field = 'nome.id';
		}
		$data = $flt['rules'][$i]['data'];
		$where = $where . "($field LIKE '%$data%')";
		if ($i < ($cnt - 1)) {
			$where = $where . ' AND ';
		}
	}
	if ($where != '') {
		$where = 'WHERE ' . $where;
	}
	$result = $sqlcn->ExecuteSQL("SELECT COUNT(*) AS cnt FROM nome");
	$row = mysqli_fetch_array($result);
	$count = $row['cnt'];
	$total_pages = ($count > 0) ? ceil($count / $limit) : 0;
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
	$sql = <<<TXT
SELECT     nome.id         AS nomeid,
           group_nome.name AS groupname,
           vendor.name     AS vendorname,
           nome.name       AS nomename,
           nome.active     AS nomeactive
FROM       nome
INNER JOIN group_nome
ON         group_nome.id = nome.groupid
INNER JOIN vendor
ON         nome.vendorid = vendor.id
$where
ORDER BY   $sidx $sord
LIMIT      $start, $limit
TXT;
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать список номенклатуры!' . mysqli_error($sqlcn->idsqlconnection));
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
		$responce->rows[$i]['id'] = $row['nomeid'];
		if ($row['nomeactive'] == '1') {
			$responce->rows[$i]['cell'] = array('<i class="fa fa-check-circle-o" aria-hidden="true"></i>', $row['nomeid'], $row['groupname'], $row['vendorname'], $row['nomename']);
		} else {
			$responce->rows[$i]['cell'] = array('<i class="fa fa-ban" aria-hidden="true"></i>', $row['nomeid'], $row['groupname'], $row['vendorname'], $row['nomename']);
		}
		$i++;
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Проверяем может ли пользователь добавлять?
	$user->TestRoles('1,4') or die('Недостаточно прав');
	$sql = "INSERT INTO knt (id, name, comment, active) VALUES (null, '$name', '$comment', 1)";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу добавить пользователя!' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'edit') {
	// Проверяем может ли пользователь редактировать?
	$user->TestRoles('1,5') or die('Недостаточно прав');
	$sql = "UPDATE nome SET name = '$nomename' WHERE id = '$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу обновить данные по номенклатуре!' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	$user->TestRoles('1,6') or die('Недостаточно прав');
	$sql = "UPDATE nome SET active = NOT active WHERE id = '$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу обновить данные по номенклатуре!' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}
