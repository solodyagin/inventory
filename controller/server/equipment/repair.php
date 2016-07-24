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

$step = GetDef('step');
$eqid = GetDef('eqid');
$oper = PostDef('oper');
$id = PostDef('id');
if ($id != '') {
	$eqid = $id;
}

if ($user->TestRoles('1,4,5,6')) {
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
            (id,dt,kntid,eqid,cost,comment,dtend,status)
VALUES      (NULL,'$dtpost','$kntid','$eqid','$cst','$comment','$dt','1')
TXT;
			$sqlcn->ExecuteSQL($sql)
					or die('Не смог добавить ремонт!: ' . mysqli_error($sqlcn->idsqlconnection));
			if ($status != 0) {
				$sql = "UPDATE equipment SET repair = '$status' WHERE id = '$eqid'";
				$sqlcn->ExecuteSQL($sql)
						or die('Не смог обновить запись о ремонте!: ' . mysqli_error($sqlcn->idsqlconnection));
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
		$result = $sqlcn->ExecuteSQL("SELECT COUNT(*) AS cnt FROM repair");
		$row = mysqli_fetch_array($result);
		$count = $row['cnt'];
		$total_pages = ($count > 0) ? ceil($count / $limit) : 0;
		if ($page > $total_pages) {
			$page = $total_pages;
		}
		$start = $limit * $page - $limit;
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
		$result = $sqlcn->ExecuteSQL($sql)
				or die('Не могу выбрать список контрагентов! ' . mysqli_error($sqlcn->idsqlconnection));
		$responce = new stdClass();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i = 0;
		while ($row = mysqli_fetch_array($result)) {
			$dtz = $row['dt'];
			$responce->rows[$i]['id'] = $row['rpid'];
			$rstatus = ($row['rstatus'] == '1') ? 'Ремонт' : 'Сделано';
			$responce->rows[$i]['cell'] = array($row['rpid'], $row['namekont'], $row['namenome'], MySQLDateToDate($row['dt']), MySQLDateToDate($row['dtend']), $row['cost'], $row['comment'], $rstatus);
			$i++;
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
			$sql = "UPDATE repair SET dt='$dt',dtend='$dtend',cost='$cost',comment='$comment',status='$rstatus' WHERE id='$eqid'";
			$sqlcn->ExecuteSQL($sql)
					or die('Не смог обновить статус ремонта! ' . mysqli_error($sqlcn->idsqlconnection));
			ReUpdateRepairEq();
			exit;
		}

		if ($oper == 'del') {
			$sql = "SELECT * FROM repair WHERE id = '$eqid'";
			$result = $sqlcn->ExecuteSQL($sql)
					or die('Не получилось выбрать список ремонтов! ' . mysqli_error($sqlcn->idsqlconnection));
			while ($row = mysqli_fetch_array($result)) {
				$status = $row['status'];
			}
			if ($status != '1') {
				$sql = "DELETE FROM repair WHERE id = '$eqid'";
				$sqlcn->ExecuteSQL($sql)
						or die('Не смог обновить статус ремонта! ' . mysqli_error($sqlcn->idsqlconnection));
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
		echo '<script>$("#messenger").addClass("alert alert-error");</script>';
		for ($i = 0; $i <= count($err); $i++) {
			echo "$err[$i]<br>";
		}
	}
}
