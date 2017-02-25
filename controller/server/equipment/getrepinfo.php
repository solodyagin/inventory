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
$eqid = GetDef('eqid');
$comment = PostDef('comment');
$dt = PostDef('dt', '10.10.2014 00:00:00');
$dtend = PostDef('dtend', '10.10.2014 00:00:00');
$status = PostDef('status', '1');
$doc = PostDef('doc');

// если не задано ТМЦ по которому показываем перемещения, то тогда просто листаем последние
if ($eqid == '') {
	$where = '';
} else {
	$where = "WHERE repair.eqid = '$eqid'";
}

if ($oper == '') {
	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$sql = <<<TXT
SELECT     COUNT(*) AS cnt,
           repair.dt,
           repair.dtend,
           repair.kntid,
           knt.name,
           repair.cost,
           repair.comment,
           repair.status
FROM       repair
INNER JOIN knt
ON         knt.id = repair.kntid
$where
TXT;
	try {
		$row = DB::prepare($sql)->execute()->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список ремонтов (1)', 0, $ex);
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
SELECT     repair.id,
           repair.userfrom,
           repair.userto,
           repair.doc,
           repair.dt,
           repair.dtend,
           repair.kntid,
           knt.name,
           repair.cost,
           repair.comment,
           repair.status
FROM       repair
INNER JOIN knt
ON         knt.id = repair.kntid
$where
ORDER BY   $sidx $sord
LIMIT      $start, $limit
TXT;
	try {
		$arr = DB::prepare($sql)->execute()->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['id'];
			$dt = MySQLDateToDate($row['dt']);
			$dtend = MySQLDateToDate($row['dtend']);
			switch ($row['status']) {
				case '0':
					$st = 'Работает';
					break;
				case '1':
					$st = 'В сервисе';
					break;
				case '2':
					$st = 'Есть заявка';
					break;
				case '3':
					$st = 'Списать';
					break;
			}
			$zz = new User();
			if ($row['userto'] != '-1') {
				$zz->GetById($row['userto']);
				$row['userto'] = $zz->fio;
			} else {
				$row['userto'] = 'не задано';
			}
			if ($row['userfrom'] != '-1') {
				$zz->GetById($row['userfrom']);
				$row['userfrom'] = $zz->fio;
			} else {
				$row['userfrom'] = 'не задано';
			}
			$responce->rows[$i]['cell'] = array($row['id'], $dt, $dtend, $row['name'],
				$row['cost'], $row['comment'], $st, $row['userfrom'],
				$row['userto'], $row['doc']);
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список ремонтов (2)', 0, $ex);
	}
	jsonExit($responce);
}

if ($oper == 'edit') {
	$dt = DateToMySQLDateTime2($dt . ' 00:00:00');
	$dtend = DateToMySQLDateTime2($dtend . ' 00:00:00');
	$sql = <<<TXT
UPDATE repair
SET    comment = :comment, dt = :dt, dtend = :dtend, status = :status, doc = :doc
WHERE  id = :id
TXT;
	try {
		DB::prepare($sql)->execute(array(
			':comment' => $comment,
			':dt' => $dt,
			':dtend' => $dtend,
			':status' => $status,
			':doc' => $doc,
			':id' => $id
		));
	} catch (PDOException $ex) {
		throw new DBException('Не могу обновить статус ремонта ТМЦ', 0, $ex);
	}
	ReUpdateRepairEq();
	exit;
}

if ($oper == 'del') {
	$sql = 'DELETE FROM repair WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не могу удалить запись о ремонте', 0, $ex);
	}
	ReUpdateRepairEq();
	exit;
}
