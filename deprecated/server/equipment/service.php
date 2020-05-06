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

//use PDOException;
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
		$doc = $req->get('doc');
		$suserid1 = $req->get('suserid1', '-1');
		$suserid2 = $req->get('suserid2', '-1');
		if (count($err) == 0) {
			try {
				$sql = <<<TXT
insert into repair (dt, kntid, eqid, cost, comment, dtend, status, userfrom, userto, doc)
values (:dtpost, :kntid, :eqid, :cost, :comment, :dtend, '1', :userfrom, :userto, :doc)
TXT;
				db::prepare($sql)->execute([
					':dtpost' => $dtpost,
					':kntid' => $kntid,
					':eqid' => $eqid,
					':cost' => $cst,
					':comment' => $comment,
					':dtend' => $dt,
					':userfrom' => $suserid1,
					':userto' => $suserid2,
					':doc' => $doc
				]);
			} catch (PDOException $ex) {
				throw new dbexception('Не смог добавить ремонт', 0, $ex);
			}

			// ставим статус "ремонт", только если нужен сервис в общем списке ТМЦ
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
	if ($step == 'edit') {
		$dt = utils::DateToMySQLDateTime2($req->get('dtpost') . ' 00:00:00');
		$dtend = utils::DateToMySQLDateTime2($req->get('dt') . ' 00:00:00');
		$cost = $req->get('cst');
		$comment = $req->get('comment');
		$status = $req->get('status');
		$doc = $req->get('doc');
		$suserid1 = $req->get('suserid1');
		$suserid2 = $req->get('suserid2');
		$kntid = $req->get('kntid');
		try {
			$sql = <<<TXT
update repair
set dt = :dt, dtend = :dtend, cost = :cost, comment = :comment, status = :status, doc = :doc,
	userfrom = :userfrom, userto = :userto, kntid = :kntid
where id = :id
TXT;
			db::prepare($sql)->execute([
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
			]);
		} catch (PDOException $ex) {
			throw new dbexception('Не смог обновить статус ремонта', 0, $ex);
		}
		utils::reUpdateRepairEq();
		exit;
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
