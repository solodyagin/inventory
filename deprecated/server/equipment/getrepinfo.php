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

// Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

//use PDO;
//use PDOException;
//use stdClass;
use core\baseuser;
use core\db;
use core\dbexception;
use core\request;
use core\utils;

$req = request::getInstance();
$page = $req->get('page', 1);
if ($page == 0) {
	$page = 1;
}
$limit = $req->get('rows');
$sidx = $req->get('sidx', '1');
$sord = $req->get('sord');
$oper = $req->get('oper');
$id = $req->get('id');
$eqid = $req->get('eqid');
$comment = $req->get('comment');
$dt = $req->get('dt', '10.10.2014 00:00:00');
$dtend = $req->get('dtend', '10.10.2014 00:00:00');
$status = $req->get('status', '1');
$doc = $req->get('doc');

// если не задано ТМЦ, по которому показываем перемещения, то тогда просто листаем последние
if ($eqid == '') {
	$where = '';
} else {
	$where = "where repair.eqid=$eqid";
}

if ($oper == '') {
	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;
	try {
		$sql = "select count(*) as cnt from repair $where";
		$row = db::prepare($sql)->execute()->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new dbexception('Не могу выбрать список ремонтов (1)', 0, $ex);
	}
	if ($count == 0) {
		utils::jsonExit($responce);
	}
	$total_pages = ceil($count / $limit);
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
	if ($start < 0) {
		utils::jsonExit($responce);
	}
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	try {
		switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
			case 'mysql':
				$sql = <<<TXT
select
	repair.id,
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
from repair
	inner join knt on knt.id = repair.kntid
$where
order by $sidx $sord
limit $start, $limit
TXT;
				break;
			case 'pgsql':
				$sql = <<<TXT
select
	repair.id,
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
from repair
	inner join knt on knt.id = repair.kntid
$where
order by $sidx $sord
offset $start limit $limit
TXT;
				break;
		}
		$arr = db::prepare($sql)->execute()->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['id'];
			$dt = utils::MySQLDateToDate($row['dt']);
			$dtend = utils::MySQLDateToDate($row['dtend']);
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
			$zz = new baseuser();
			if ($row['userto'] != '-1') {
				$zz->getById($row['userto']);
				$row['userto'] = $zz->fio;
			} else {
				$row['userto'] = 'не задано';
			}
			if ($row['userfrom'] != '-1') {
				$zz->getById($row['userfrom']);
				$row['userfrom'] = $zz->fio;
			} else {
				$row['userfrom'] = 'не задано';
			}
			$responce->rows[$i]['cell'] = [
				$row['id'], $dt, $dtend, $row['name'],
				$row['cost'], $row['comment'], $st, $row['userfrom'],
				$row['userto'], $row['doc']
			];
			$i++;
		}
	} catch (PDOException $ex) {
		throw new dbexception('Не могу выбрать список ремонтов (2)', 0, $ex);
	}
	utils::jsonExit($responce);
}

if ($oper == 'edit') {
	$dt = utils::DateToMySQLDateTime2($dt . ' 00:00:00');
	$dtend = utils::DateToMySQLDateTime2($dtend . ' 00:00:00');
	try {
		$sql = <<<TXT
update repair
set comment = :comment, dt = :dt, dtend = :dtend, status = :status, doc = :doc
where id = :id
TXT;
		db::prepare($sql)->execute([
			':comment' => $comment,
			':dt' => $dt,
			':dtend' => $dtend,
			':status' => $status,
			':doc' => $doc,
			':id' => $id
		]);
	} catch (PDOException $ex) {
		throw new dbexception('Не могу обновить статус ремонта ТМЦ', 0, $ex);
	}
	utils::reUpdateRepairEq();
	exit;
}

if ($oper == 'del') {
	try {
		$sql = 'delete from repair where id = :id';
		db::prepare($sql)->execute([':id' => $id]);
	} catch (PDOException $ex) {
		throw new dbexception('Не могу удалить запись о ремонте', 0, $ex);
	}
	utils::reUpdateRepairEq();
	exit;
}
