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
use core\db;
use core\dbexception;
use core\request;
use core\user;
use core\utils;

$req = request::getInstance();
$step = $req->get('step');
$eqid = $req->get('eqid');
$oper = $req->get('oper');
$id = $req->get('id');
if ($id != '') {
	$eqid = $id;
}

$user = user::getInstance();
if ($user->isAdmin() || $user->testRights([1, 4, 5, 6])) {
	if ($step == 'add') {
		$dtpost = utils::DateToMySQLDateTime2($req->get('dtpost') . ' 00:00:00');
		if ($dtpost == '') {
			$err[] = 'Не выбрана дата!';
		}
		$dt = utils::DateToMySQLDateTime2($req->get('dt') . ' 00:00:00');
		if ($dt == '') {
			$err[] = 'Не выбрана дата!';
		}
		$kntid = $req->get('kntid');
		if ($kntid == '') {
			$err[] = 'Не выбран контрагент!';
		}
		$cst = $req->get('cst');
		$status = $req->get('status');
		$comment = $req->get('comment');
		if (count($err) == 0) {
			try {
				$sql = <<<TXT
INSERT INTO repair (dt, kntid, eqid, cost, comment, dtend, status, userfrom, userto, doc)
VALUES (:dt, :kntid, :eqid, :cost, :comment, :dtend, '1', 0, 0, '')
TXT;
				db::prepare($sql)->execute([
					':dt' => $dtpost,
					':kntid' => $kntid,
					':eqid' => $eqid,
					':cost' => $cst,
					':comment' => $comment,
					':dtend' => $dt
				]);
			} catch (PDOException $ex) {
				throw new dbexception('Не смог добавить ремонт', 0, $ex);
			}

			if ($status != 0) {
				try {
					$sql = 'update equipment set repair = :repair where id = :id';
					db::prepare($sql)->execute([':repair' => $status, ':id' => $eqid]);
				} catch (PDOException $ex) {
					throw new dbexception('Не смог обновить запись о ремонте', 0, $ex);
				}
			}
		}
	}

	if ($step == 'list') {
		$page = $req->get('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = $req->get('rows');
		$sidx = $req->get('sidx', '1');
		$sord = $req->get('sord');
		$oper = $req->get('oper');
		$id = $req->get('id');
		$where = ($id != '') ? "where reqid=$id" : '';
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'select count(*) as cnt from repair';
			$row = db::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new dbexception('', 0, $ex);
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
	rp2.reqid as reqid,
	rp2.rstatus as rstatus,
	rp2.rpid as rpid,
	knt.id as kntid,
	knt.name as namekont,
	rp2.kntid,
	rp2.dt,
	rp2.cost,
	rp2.comment,
	rp2.dtend,
	rp2.nomeid,
	rp2.name as namenome
from knt
	inner join (
		select
			rp.reqid as reqid,
			rp.rstatus as rstatus,
			rp.rpid as rpid,
			nome.name,
			rp.kntid,
			rp.dt,
			rp.cost,
			rp.comment,
			rp.dtend,
			rp.nomeid
		from nome
			inner join (
				select
					repair.eqid as reqid,
					repair.status as rstatus,
					repair.id as rpid,
					repair.kntid,
					repair.dt,
					repair.cost,
					repair.comment,
					repair.dtend,
					equipment.nomeid
				from repair
					inner join equipment on repair.eqid = equipment.id
			) as rp on rp.nomeid = nome.id
	) as rp2 on rp2.kntid = knt.id
$where
order by $sidx $sord
limit $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select
	rp2.reqid as reqid,
	rp2.rstatus as rstatus,
	rp2.rpid as rpid,
	knt.id as kntid,
	knt.name as namekont,
	rp2.kntid,
	rp2.dt,
	rp2.cost,
	rp2.comment,
	rp2.dtend,
	rp2.nomeid,
	rp2.name as namenome
from knt
	inner join (
		select
			rp.reqid as reqid,
			rp.rstatus as rstatus,
			rp.rpid as rpid,
			nome.name,
			rp.kntid,
			rp.dt,
			rp.cost,
			rp.comment,
			rp.dtend,
			rp.nomeid
		from nome
			inner join (
				select
					repair.eqid as reqid,
					repair.status as rstatus,
					repair.id as rpid,
					repair.kntid,
					repair.dt,
					repair.cost,
					repair.comment,
					repair.dtend,
					equipment.nomeid
				from repair
					inner join equipment on repair.eqid = equipment.id
			) as rp on rp.nomeid = nome.id
	) as rp2 on rp2.kntid = knt.id
$where
order by $sidx $sord
offset $start limit $limit
TXT;
					break;
			}
			$rows = db::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($rows as $row) {
				$dtz = $row['dt'];
				$responce->rows[$i]['id'] = $row['rpid'];
				$rstatus = ($row['rstatus'] == '1') ? 'Ремонт' : 'Сделано';
				$responce->rows[$i]['cell'] = [
					$row['rpid'], $row['namekont'], $row['namenome'],
					utils::MySQLDateToDate($row['dt']),
					utils::MySQLDateToDate($row['dtend']), $row['cost'], $row['comment'], $rstatus];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список контрагентов', 0, $ex);
		}
		utils::jsonExit($responce);
	}

	if ($step == 'edit') {
		if ($oper == 'edit') {
			$dt = utils::DateToMySQLDateTime2($req->get('dt') . ' 00:00:00');
			$dtend = utils::DateToMySQLDateTime2($req->get('dtend') . ' 00:00:00');
			$cost = $req->get('cost');
			$comment = $req->get('comment');
			$rstatus = $req->get('rstatus');
			try {
				$sql = <<<TXT
update repair
set dt = :dt, dtend = :dtend, cost = :cost, comment = :comment, status = :status
where id = :id
TXT;
				db::prepare($sql)->execute([
					':dt' => $dt,
					':dtend' => $dtend,
					':cost' => $cost,
					':comment' => $comment,
					':status' => $rstatus,
					':id' => $eqid
				]);
			} catch (PDOException $ex) {
				throw new dbexception('Не смог обновить статус ремонта', 0, $ex);
			}
			utils::reUpdateRepairEq();
			exit;
		}

		if ($oper == 'del') {
//			try {
//				$sql = 'select * from repair where id = :id';
//				$row = db::prepare($sql)->execute([':id' => $eqid])->fetch();
//				if ($row) {
//					$status = $row['status'];
//				}
//			} catch (PDOException $ex) {
//				throw new dbexception('Не получилось выбрать список ремонтов', 0, $ex);
//			}
//			if ($status != '1') {
//				try {
//					$sql = 'delete from repair where id = :id';
//					db::prepare($sql)->execute([':id' => $eqid]);
//				} catch (PDOException $ex) {
//					throw new dbexception('Не смог обновить статус ремонта', 0, $ex);
//				}
//			}
			try {
				$sql = 'delete from repair where id = :id and status <> 1';
				db::prepare($sql)->execute([':id' => $eqid]);
			} catch (PDOException $ex) {
				throw new dbexception('Не смог обновить статус ремонта', 0, $ex);
			}
			utils::reUpdateRepairEq();
			exit;
		}
	}
}

if ($step != 'list') {
	if (count($err) == 0) {
		echo 'ok';
	} else {
		echo '<script>$("#messenger").addClass("alert alert-danger");</script>';
		echo implode('<br>', $err);
	}
}
