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
$title = PostDef('title');
if (!empty($title)) {
	$title = ClearMySqlString($sqlcn->idsqlconnection, $title);
}
$stiker = PostDef('stiker');

if ($oper == '') {
	// Проверка: может ли пользователь просматривать?
	(($user->mode == 1) || $user->TestRoles('1,3,4,5,6')) or die('Недостаточно прав');

	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$count = 0;

	$sql = 'SELECT COUNT(*) AS cnt FROM news';
	try {
		$row = DB::prepare($sql)->execute()->fetch();
		if ($row) {
			$count = $row['cnt'];
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список новостей!', 0, $ex);
	}

	if ($count > 0) {
		$total_pages = ceil($count / $limit);
		if ($page > $total_pages) {
			$page = $total_pages;
		}
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;

		$start = $limit * $page - $limit;

		$sql = "SELECT * FROM news ORDER BY $sidx $sord LIMIT $start, $limit";
		try {
			$arr = DB::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$responce->rows[$i]['cell'] = array($row['id'], $row['dt'], $row['title'], $row['stiker']);
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список новостей!', 0, $ex);
		}
	}
	jsonExit($responce);
}

if ($oper == 'edit') {
	// Проверка: может ли пользователь редактировать?
	(($user->mode == 1) || $user->TestRoles('1,5')) or die('Недостаточно прав');

	$sql = 'UPDATE news SET title = :title, stiker = :stiker WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(
			':title' => $title,
			':stiker' => $stiker,
			':id' => $id
		));
	} catch (PDOException $ex) {
		throw new DBException('Не могу обновить заголовок новости!', 0, $ex);
	}
	exit;
}

if ($oper == 'del') {
	// Проверка: может ли пользователь удалять?
	(($user->mode == 1) || $user->TestRoles('1,6')) or die('Недостаточно прав');

	$sql = 'DELETE FROM news WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не могу удалить новость!', 0, $ex);
	}
	exit;
}
