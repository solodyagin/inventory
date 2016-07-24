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
$idcontract = GetDef('idcontract');
$where = "WHERE idcontract = '$idcontract'";

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	$user->TestRoles('1,3,4,5,6') or die('Недостаточно прав');
	$result = $sqlcn->ExecuteSQL("SELECT COUNT(*) AS cnt FROM files_contract $where");
	$row = mysqli_fetch_array($result);
	$count = $row['cnt'];
	$total_pages = ($count > 0) ? ceil($count / $limit) : 0;
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
	$sql = "SELECT * FROM files_contract $where ORDER BY $sidx $sord LIMIT $start, $limit";
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать список договоров! ' . mysqli_error($sqlcn->idsqlconnection));
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
		$responce->rows[$i]['id'] = $row['id'];
		$filename = $row['filename'];
		$userfreandlyfilename = $row['userfreandlyfilename'];
		if ($userfreandlyfilename == '') {
			$userfreandlyfilename = 'Посмотреть';
		}
		$responce->rows[$i]['cell'] = array($row['id'], "<a target=\"_blank\" href=\"files/$filename\">$userfreandlyfilename</a>");
		$i++;
	}
	jsonExit($responce);
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	$user->TestRoles('1,6') or die('Для удаления не хватает прав!');
	$sql = "DELETE FROM files_contract WHERE id = '$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не смог удалить файл! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}
