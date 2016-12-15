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

$step = GetDef('step');
$eqid = GetDef('eqid');
$oper = PostDef('oper');
$id = PostDef('id');
if ($id != '') {
	$eqid = $id;
}

if (($user->mode == 1) || $user->TestRoles('1,4,5,6')) {
	if ($step == 'add') {
		$dtpost = DateToMySQLDateTime2(PostDef('dtpost') . ' 00:00:00');
		if ($dtpost == '') {
			$err[] = 'Не выбрана дата!';
		}
		$dt = DateToMySQLDateTime2(PostDef('dt') . ' 00:00:00');
		if ($dt == '') {
			$err[] = 'Не выбрана дата!';
		}
		$kntid = PostDef('kntid');
		if ($kntid == '') {
			$err[] = 'Не выбран контрагент!';
		}
		$cst = PostDef('cst');
		$status = PostDef('status');
		$comment = PostDef('comment');
		if (count($err) == 0) {
			$sql = <<<TXT
INSERT INTO repair
            (id, dt, kntid, eqid, cost, comment, dtend, status)
VALUES      (NULL, :dt, :kntid, :eqid, :cost, :comment, :dtend, '1')
TXT;
			try {
				DB::prepare($sql)->execute(array(
					':dt' => $dtpost,
					':kntid' => $kntid,
					':eqid' => $eqid,
					':cost' => $cst,
					':comment' => $comment,
					':dtend' => $dt
				));
			} catch (PDOException $ex) {
				throw new DBException('Не смог добавить ремонт', 0, $ex);
			}

			if ($status != 0) {
				$sql = 'UPDATE equipment SET repair = :repair WHERE id = :id';
				try {
					DB::prepare($sql)->execute(array(
						':repair' => $status,
						':id' => $eqid
					));
				} catch (PDOException $ex) {
					throw new DBException('Не смог обновить запись о ремонте', 0, $ex);
				}
			}
		}
	}

	if ($step == 'list') {
		$page = GetDef('page', '1');
		$limit = GetDef('rows');
		$sidx = GetDef('sidx', '1');
		$sord = GetDef('sord');
		$oper = PostDef('oper');
		$id = GetDef('id');
		$where = ($id != '') ? "WHERE reqid = '$id'" : '';

		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;

		$sql = 'SELECT COUNT(*) AS cnt FROM repair';
		try {
			$row = DB::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('', 0, $ex);
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
SELECT     rp2.reqid   AS reqid,
           rp2.rstatus AS rstatus,
           rp2.rpid    AS rpid,
           knt.id      AS kntid,
           knt.name    AS namekont,
           rp2.kntid,
           rp2.dt,
           rp2.cost,
           rp2.comment,
           rp2.dtend,
           rp2.nomeid,
           rp2.name AS namenome
FROM       knt
INNER JOIN
           (
                      SELECT     rp.reqid   AS reqid,
                                 rp.rstatus AS rstatus,
                                 rp.rpid    AS rpid,
                                 nome.name,
                                 rp.kntid,
                                 rp.dt,
                                 rp.cost,
                                 rp.comment,
                                 rp.dtend,
                                 rp.nomeid
                      FROM       nome
                      INNER JOIN
                                 (
                                            SELECT     repair.eqid   AS reqid,
                                                       repair.status AS rstatus,
                                                       repair.id     AS rpid,
                                                       repair.kntid,
                                                       repair.dt,
                                                       repair.cost,
                                                       repair.comment,
                                                       repair.dtend,
                                                       equipment.nomeid
                                            FROM       repair
                                            INNER JOIN equipment
                                            ON         repair.eqid = equipment.id) AS rp
                      ON         rp.nomeid = nome.id) AS rp2
ON         rp2.kntid = knt.id
$where
ORDER BY   $sidx $sord
LIMIT      $start, $limit
TXT;
		try {
			$arr = DB::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$dtz = $row['dt'];
				$responce->rows[$i]['id'] = $row['rpid'];
				$rstatus = ($row['rstatus'] == '1') ? 'Ремонт' : 'Сделано';
				$responce->rows[$i]['cell'] = array($row['rpid'], $row['namekont'], $row['namenome'],
					MySQLDateToDate($row['dt']), MySQLDateToDate($row['dtend']), $row['cost'],
					$row['comment'], $rstatus);
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список контрагентов', 0, $ex);
		}
		jsonExit($responce);
	}

	if ($step == 'edit') {
		if ($oper == 'edit') {
			$dt = DateToMySQLDateTime2(PostDef('dt') . ' 00:00:00');
			$dtend = DateToMySQLDateTime2(PostDef('dtend') . ' 00:00:00');
			$cost = PostDef('cost');
			$comment = PostDef('comment');
			$rstatus = PostDef('rstatus');
			$sql = <<<TXT
UPDATE repair
SET dt = :dt, dtend = :dtend, cost = :cost, comment = :comment, status = :status
WHERE id = :id'
TXT;
			try {
				DB::prepare($sql)->execute(array(
					':dt' => $dt,
					':dtend' => $dtend,
					':cost' => $cost,
					':comment' => $comment,
					':status' => $rstatus,
					':id' => $eqid
				));
			} catch (PDOException $ex) {
				throw new DBException('Не смог обновить статус ремонта', 0, $ex);
			}
			ReUpdateRepairEq();
			exit;
		}

		if ($oper == 'del') {
//			$sql = 'SELECT * FROM repair WHERE id = :id';
//			try {
//				$row = DB::prepare($sql)->execute(array(':id' => $eqid))->fetch();
//				if ($row) {
//					$status = $row['status'];
//				}
//			} catch (PDOException $ex) {
//				throw new DBException('Не получилось выбрать список ремонтов', 0, $ex);
//			}
//			if ($status != '1') {
//				$sql = 'DELETE FROM repair WHERE id = :id';
//				try {
//					DB::prepare($sql)->execute(array(':id' => $eqid));
//				} catch (PDOException $ex) {
//					throw new DBException('Не смог обновить статус ремонта', 0, $ex);
//				}
//			}
			$sql = 'DELETE FROM `repair` WHERE id = :id AND `status` <> 1';
			try {
				DB::prepare($sql)->execute(array(':id' => $eqid));
			} catch (PDOException $ex) {
				throw new DBException('Не смог обновить статус ремонта', 0, $ex);
			}
			ReUpdateRepairEq();
			exit;
		}
	}
}

if ($step != 'list') {
	if (count($err) == 0) {
		echo 'ok';
	} else {
		echo '<script>$("#messenger").addClass("alert alert-danger");</script>';
		for ($i = 0; $i <= count($err); $i++) {
			echo "$err[$i]<br>";
		}
	}
}
