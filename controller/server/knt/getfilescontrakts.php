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

$page = GetDef('page', '1');
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$oper = PostDef('oper');
$id = PostDef('id');
$idcontract = GetDef('idcontract');

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	(($user->mode == 1) || $user->TestRoles('1,3,4,5,6')) or die('Недостаточно прав');

	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$count = 0;

	$sql = 'SELECT COUNT(*) AS cnt FROM files_contract WHERE idcontract = :idcontract';
	try {
		$row = DB::prepare($sql)->execute(array(':idcontract' => $idcontract))->fetch();
		if ($row) {
			$count = $row['cnt'];
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список договоров (1)', 0, $ex);
	}

	if ($count > 0) {
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

		$sql = "SELECT * FROM files_contract WHERE idcontract = :idcontract ORDER BY $sidx $sord LIMIT $start, $limit";
		try {
			$arr = DB::prepare($sql)->execute(array(':idcontract' => $idcontract))->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$filename = $row['filename'];
				$userfreandlyfilename = $row['userfreandlyfilename'];
				if ($userfreandlyfilename == '') {
					$userfreandlyfilename = 'Посмотреть';
				}
				$responce->rows[$i]['cell'] = array($row['id'], "<a target=\"_blank\" href=\"files/$filename\">$userfreandlyfilename</a>");
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список договоров (2)', 0, $ex);
		}
	}
	jsonExit($responce);
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	(($user->mode == 1) || $user->TestRoles('1,6')) or die('Для удаления не хватает прав!');

	$sql = 'DELETE FROM files_contract WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не смог удалить файл', 0, $ex);
	}
	exit;
}
