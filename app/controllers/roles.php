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

class roles extends controller {

	/** Для работы jqGrid */
	function list() {
		$user = user::getInstance();
		// Разрешаем при наличии ролей "Полный доступ" и "Просмотр"
		($user->isAdmin() || $user->testRights([1, 3])) or die('Недостаточно прав');
		$req = request::getInstance();
		$page = $req->get('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = $req->get('rows');
		$sidx = $req->get('sidx', '1');
		$sord = $req->get('sord');
		$role = $req->get('role');
		$userid = $req->get('userid');
		// Роли
		$roles = [
			'1' => 'Полный доступ',
			'2' => 'Просмотр финансовых отчетов',
			'3' => 'Просмотр',
			'4' => 'Добавление',
			'5' => 'Редактирование',
			'6' => 'Удаление',
			'7' => 'Отправка СМС',
			'8' => 'Манипуляции с деньгами',
			'9' => 'Редактирование карт'
		];
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'select count(*) as cnt from usersroles where userid = :userid';
			$row = db::prepare($sql)->execute([':userid' => $userid])->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список ролей пользователей (1)', 0, $ex);
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
select *
from usersroles
where userid = :userid
order by $sidx $sord
limit $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select *
from usersroles
where userid = :userid
order by $sidx $sord
offset $start limit $limit
TXT;
					break;
			}
			$arr = db::prepare($sql)->execute([':userid' => $userid])->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$rowid = $row['id'];
				$responce->rows[$i]['id'] = $rowid;
				$role = $roles[$row['role']];
				$responce->rows[$i]['cell'] = [$rowid, $role];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список ролей пользователей (2)', 0, $ex);
		}
		utils::jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = user::getInstance();
		$req = request::getInstance();
		$oper = $req->get('oper');
		$id = $req->get('id');
		$role = $req->get('role');
		$userid = $req->get('userid');
		switch ($oper) {
			case 'add':
				// Только с полными правами можно добавлять роль!
				($user->isAdmin() || $user->testRights([1])) or die('Недостаточно прав');
				try {
					$sql = 'select count(*) cnt from usersroles where userid = :userid and role = :role';
					$row = db::prepare($sql)->execute([':userid' => $userid, ':role' => $role])->fetch();
					$count = ($row) ? $row['cnt'] : 0;
					if ($count == 0) {
						$sql = 'insert into usersroles (userid, role) values (:userid, :role)';
						db::prepare($sql)->execute([':userid' => $userid, ':role' => $role]);
					}
				} catch (PDOException $ex) {
					throw new dbexception('Не могу добавить роль пользователя', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем может ли пользователь удалять?
				($user->isAdmin() || $user->testRights([1])) or die('Для удаления недостаточно прав');
				try {
					$sql = 'delete from usersroles where id = :id';
					db::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу удалить роль пользователя', 0, $ex);
				}
				break;
		}
	}

}
