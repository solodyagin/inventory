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

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

$page = GetDef('page', 1);
if ($page == 0) {
	$page = 1;
}
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$oper = PostDef('oper');
$id = PostDef('id');
$idknt = GetDef('idknt');
$name = PostDef('name');
$num = PostDef('num');
$datestart = PostDef('datestart');
$dateend = PostDef('dateend');
$work = PostDef('work');
$comment = PostDef('comment');

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	(($user->mode == 1) || $user->TestRights([1,3,4,5,6])) or die('Недостаточно прав');

	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$sql = 'SELECT COUNT(*) AS cnt FROM contract WHERE kntid = :kntid';
	try {
		$row = DB::prepare($sql)->execute(array(':kntid' => $idknt))->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список договоров (1)', 0, $ex);
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

	$sql = "SELECT * FROM contract WHERE kntid = :kntid ORDER BY $sidx $sord LIMIT $start, $limit";
	try {
		$arr = DB::prepare($sql)->execute(array(':kntid' => $idknt))->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['id'];
			$work = ($row['work'] == 0) ? 'No' : 'Yes';
			$dateend = $row['dateend'] . ' 00:00:00';
			$datestart = $row['datestart'] . ' 00:00:00';
			$ic = ($row['active'] == '1') ? 'fa-check-circle-o' : 'fa-ban';
			$responce->rows[$i]['cell'] = array(
				"<i class=\"fa $ic\" aria-hidden=\"true\"></i>",
				$row['id'],
				$row['num'],
				$row['name'],
				MySQLDateTimeToDateTime($datestart),
				MySQLDateTimeToDateTime($dateend),
				$work,
				$row['comment']
			);
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список договоров (2)', 0, $ex);
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Проверяем может ли пользователь добавлять?
	(($user->mode == 1) || $user->TestRights([1,4])) or die('Недостаточно прав');

	$work = ($work == 'Yes') ? '1' : '0';
	$datestart = DateToMySQLDateTime2($datestart);
	$dateend = DateToMySQLDateTime2($dateend);
	$sql = <<<TXT
INSERT INTO contract
            (id,kntid,num,name,comment,datestart,dateend,work,active)
VALUES      (NULL, :kntid, :num, :name, :comment, :datestart, :dateend, :work, 1)
TXT;
	try {
		DB::prepare($sql)->execute(array(
			':kntid' => $idknt,
			':num' => $num,
			':name' => $name,
			':comment' => $comment,
			':datestart' => $datestart,
			':dateend' => $dateend,
			':work' => $work,
		));
	} catch (PDOException $ex) {
		throw new DBException('Не могу добавить данные по договору', 0, $ex);
	}
	exit;
}

if ($oper == 'edit') {
	// Проверяем может ли пользователь редактировать?
	(($user->mode == 1) || $user->TestRights([1,5])) or die('Для редактирования не хватает прав!');

	$work = ($work == 'Yes') ? '1' : '0';
	$datestart = DateToMySQLDateTime2($datestart);
	$dateend = DateToMySQLDateTime2($dateend);
	$sql = <<<TXT
UPDATE contract
SET    num = :num,name = :name,comment = :comment,datestart = :datestart,dateend = :dateend,work = :work
WHERE  id = :id
TXT;
	try {
		DB::prepare($sql)->execute(array(
			':num' => $num,
			':name' => $name,
			':comment' => $comment,
			':datestart' => $datestart,
			':dateend' => $dateend,
			':work' => $work,
			':id' => $id
		));
	} catch (PDOException $ex) {
		throw new DBException('Не могу обновить данные по договору', 0, $ex);
	}
	exit;
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	(($user->mode == 1) || $user->TestRights([1,6])) or die('Для удаления не хватает прав!');

	$sql = 'UPDATE contract SET active = NOT active WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не смог пометить на удаление договор', 0, $ex);
	}
	exit;
}
