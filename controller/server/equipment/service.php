<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

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
		$doc = PostDef('doc');
		$suserid1 = PostDef('suserid1', '-1');
		$suserid2 = PostDef('suserid2', '-1');
		if (count($err) == 0) {
			$sql = <<<TXT
INSERT INTO repair
            (id,dt,kntid,eqid,cost,comment,dtend,status,userfrom,userto,doc)
VALUES      (NULL, :dtpost, :kntid, :eqid, :cost, :comment, :dtend, '1', :userfrom, :userto, :doc)
TXT;
			try {
				DB::prepare($sql)->execute(array(
					':dt' => $dtpost,
					':kntid' => $kntid,
					':eqid' => $eqid,
					':cost' => $cst,
					':comment' => $comment,
					':dtend' => $dt,
					':userfrom' => $suserid1,
					':userto' => $suserid2,
					':doc' => $doc
				));
			} catch (PDOException $ex) {
				throw new DBException('Не смог добавить ремонт', 0, $ex);
			}

			// ставим статус "ремонт", только если нужен сервис в общем списке ТМЦ
			if ($status != 0) {
				$sql = 'UPDATE equipment SET repair = :repair WHERE id = :id';
				try {
					DB::prepare($sql)->execute(array(':repair' => $status, ':id' => $eqid));
				} catch (PDOException $ex) {
					throw new DBException('Не смог обновить запись о ремонте', 0, $ex);
				}
			}
		}
	}
	if ($step == 'edit') {
		$dt = DateToMySQLDateTime2(PostDef('dtpost') . ' 00:00:00');
		$dtend = DateToMySQLDateTime2(PostDef('dt') . ' 00:00:00');
		$cost = PostDef('cst');
		$comment = PostDef('comment');
		$status = PostDef('status');
		$doc = PostDef('doc');
		$suserid1 = PostDef('suserid1');
		$suserid2 = PostDef('suserid2');
		$kntid = PostDef('kntid');
		$sql = <<<TXT
UPDATE repair
SET    dt = :dt,dtend = :dtend,cost = :cost,comment = :comment,status = :status,doc = :doc,
       userfrom = :userfrom,userto = :userto,kntid = :kntid
WHERE  id = :id
TXT;
		try {
			DB::prepare($sql)->execute(array(
				':dt' => $dt,
				':dtend' => $dtend,
				':cost' => $cost,
				':comment' => $comment,
				':status' => $status,
				':doc' => $doc,
				':userfrom' => $suserid1,
				':userto' => $suserid2,
				':kntid' => $kntid,
				':id' => $eqid
			));
		} catch (PDOException $ex) {
			throw new DBException('Не смог обновить статус ремонта', 0, $ex);
		}
		ReUpdateRepairEq();
		exit;
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
