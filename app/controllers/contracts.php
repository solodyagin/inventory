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

namespace app\controllers;

use PDO;
use PDOException;
use stdClass;
use core\controller;
use core\request;
use core\user;
use core\db;
use core\dbexception;
use core\utils;

class contracts extends controller {

	/** Для работы jqGrid */
	function list() {
		// Проверяем: может ли пользователь просматривать?
		$user = user::getInstance();
		($user->isAdmin() || $user->testRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
		$req = request::getInstance();
		$page = $req->get('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = $req->get('rows');
		$sidx = $req->get('sidx', '1');
		$sord = $req->get('sord');
		$idknt = $req->get('idknt');
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'select count(*) cnt from contract where kntid = :kntid';
			$row = db::prepare($sql)->execute([':kntid' => $idknt])->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список договоров (1)', 0, $ex);
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
select * from contract
where kntid = :kntid
order by $sidx $sord
limit $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select * from contract
where kntid = :kntid
order by $sidx $sord
offset $start limit $limit
TXT;
					break;
			}
			$arr = db::prepare($sql)->execute([':kntid' => $idknt])->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$dateend = $row['dateend'] . ' 00:00:00';
				$datestart = $row['datestart'] . ' 00:00:00';
				$ic = ($row['active'] == '1') ? 'fa-check-circle' : 'fa-ban';
				$responce->rows[$i]['cell'] = [
					"<i class=\"fas $ic\"></i>",
					$row['id'], $row['num'], $row['name'],
					utils::MySQLDateTimeToDateTime($datestart),
					utils::MySQLDateTimeToDateTime($dateend),
					$row['work'], $row['comment']
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список договоров (2)', 0, $ex);
		}
		utils::jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = user::getInstance();
		$req = request::getInstance();
		$oper = $req->get('oper');
		$id = $req->get('id');
		$idknt = $req->get('idknt');
		$name = $req->get('name');
		$num = $req->get('num');
		$datestart = $req->get('datestart');
		$dateend = $req->get('dateend');
		$work = $req->get('work');
		$comment = $req->get('comment');
		switch ($oper) {
			case 'add':
				//* Проверяем: может ли пользователь добавлять?
				($user->isAdmin() || $user->testRights([1, 4])) or die('Недостаточно прав');
				$datestart = utils::DateToMySQLDateTime2($datestart);
				$dateend = utils::DateToMySQLDateTime2($dateend);
				try {
					switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							$sql = <<<TXT
insert into contract (id, kntid, num, name, comment, datestart, dateend, work, active)
values (null, :kntid, :num, :name, :comment, :datestart, :dateend, :work, 1)
TXT;
							break;
						case 'pgsql':
							$sql = <<<TXT
insert into contract (kntid, num, name, comment, datestart, dateend, work, active)
values (:kntid, :num, :name, :comment, :datestart, :dateend, :work, 1)
TXT;
							break;
					}
					db::prepare($sql)->execute([
						':kntid' => $idknt,
						':num' => $num,
						':name' => $name,
						':comment' => $comment,
						':datestart' => $datestart,
						':dateend' => $dateend,
						':work' => $work,
					]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу добавить данные по договору', 0, $ex);
				}
				break;
			case 'edit':
				// Проверяем: может ли пользователь редактировать?
				($user->isAdmin() || $user->testRights([1, 5])) or die('Для редактирования не хватает прав!');
				$datestart = utils::DateToMySQLDateTime2($datestart);
				$dateend = utils::DateToMySQLDateTime2($dateend);
				try {
					$sql = <<<TXT
update contract
set num = :num, name = :name, comment = :comment, datestart = :datestart, dateend = :dateend, work = :work
where id = :id
TXT;
					db::prepare($sql)->execute([
						':num' => $num,
						':name' => $name,
						':comment' => $comment,
						':datestart' => $datestart,
						':dateend' => $dateend,
						':work' => $work,
						':id' => $id
					]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу обновить данные по договору', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->testRights([1, 6])) or die('Для удаления не хватает прав!');
				try {
					switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							$sql = 'update contract set active = not active where id = :id';
							break;
						case 'pgsql':
							$sql = 'update contract set active = active # 1 where id = :id';
							break;
					}
					db::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не смог пометить на удаление договор', 0, $ex);
				}
				break;
		}
	}

}
