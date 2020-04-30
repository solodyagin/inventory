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

//namespace App\Controllers;
//use Core\Controller;
//use Core\Config;
//use Core\Router;
//use Core\User;
//use Core\DB;
//use \PDOException;
//use Core\DBException;

class Controller_Roles extends Controller {

	/** Для работы jqGrid */
	function list() {
		$user = User::getInstance();
		// Разрешаем при наличии ролей "Полный доступ" и "Просмотр"
		($user->isAdmin() || $user->TestRights([1, 3])) or die('Недостаточно прав');
		$page = GetDef('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = GetDef('rows');
		$sidx = GetDef('sidx', '1');
		$sord = GetDef('sord');
		$role = PostDef('role');
		$userid = GetDef('userid');
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
			$row = DB::prepare($sql)->execute([':userid' => $userid])->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список ролей пользователей (1)', 0, $ex);
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
		try {
			switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
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
			$arr = DB::prepare($sql)->execute([':userid' => $userid])->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$rowid = $row['id'];
				$responce->rows[$i]['id'] = $rowid;
				$role = $roles[$row['role']];
				$responce->rows[$i]['cell'] = [$rowid, $role];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список ролей пользователей (2)', 0, $ex);
		}
		jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = User::getInstance();
		$oper = PostDef('oper');
		$id = PostDef('id');
		$role = PostDef('role');
		$userid = GetDef('userid');
		switch ($oper) {
			case 'add':
				// Только с полными правами можно добавлять роль!
				($user->isAdmin() || $user->TestRights([1])) or die('Недостаточно прав');
				try {
					$sql = 'select count(*) cnt from usersroles where userid = :userid and role = :role';
					$row = DB::prepare($sql)->execute([':userid' => $userid, ':role' => $role])->fetch();
					$count = ($row) ? $row['cnt'] : 0;
					if ($count == 0) {
						$sql = 'insert into usersroles (userid, role) values (:userid, :role)';
						DB::prepare($sql)->execute([':userid' => $userid, ':role' => $role]);
					}
				} catch (PDOException $ex) {
					throw new DBException('Не могу добавить роль пользователя', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем может ли пользователь удалять?
				($user->isAdmin() || $user->TestRights([1])) or die('Для удаления недостаточно прав');
				try {
					$sql = 'delete from usersroles where id = :id';
					DB::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу удалить роль пользователя', 0, $ex);
				}
				break;
		}
	}

}
