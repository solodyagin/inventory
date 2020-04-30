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

class Controller_Contracts extends Controller {

	/** Для работы jqGrid */
	function list() {
		$user = User::getInstance();
		// Проверяем: может ли пользователь просматривать?
		($user->isAdmin() || $user->TestRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
		$page = GetDef('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = GetDef('rows');
		$sidx = GetDef('sidx', '1');
		$sord = GetDef('sord');
		$idknt = GetDef('idknt');
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'select count(*) cnt from contract where kntid = :kntid';
			$row = DB::prepare($sql)->execute([':kntid' => $idknt])->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список договоров (1)', 0, $ex);
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
			$arr = DB::prepare($sql)->execute([':kntid' => $idknt])->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$dateend = $row['dateend'] . ' 00:00:00';
				$datestart = $row['datestart'] . ' 00:00:00';
				$ic = ($row['active'] == '1') ? 'fa-check-circle' : 'fa-ban';
				$responce->rows[$i]['cell'] = [
					"<i class=\"fas $ic\"></i>",
					$row['id'], $row['num'], $row['name'],
					MySQLDateTimeToDateTime($datestart),
					MySQLDateTimeToDateTime($dateend),
					$row['work'], $row['comment']
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список договоров (2)', 0, $ex);
		}
		jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = User::getInstance();
		$oper = PostDef('oper');
		$id = PostDef('id');
		$idknt = GetDef('idknt');
		$name = PostDef('name');
		$num = PostDef('num');
		$datestart = PostDef('datestart');
		$dateend = PostDef('dateend');
		$work = PostDef('work');
		$comment = PostDef('comment');
		switch ($oper) {
			case 'add':
				//* Проверяем: может ли пользователь добавлять?
				($user->isAdmin() || $user->TestRights([1, 4])) or die('Недостаточно прав');
				$datestart = DateToMySQLDateTime2($datestart);
				$dateend = DateToMySQLDateTime2($dateend);
				try {
					switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
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
					DB::prepare($sql)->execute([
						':kntid' => $idknt,
						':num' => $num,
						':name' => $name,
						':comment' => $comment,
						':datestart' => $datestart,
						':dateend' => $dateend,
						':work' => $work,
					]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу добавить данные по договору', 0, $ex);
				}
				break;
			case 'edit':
				// Проверяем: может ли пользователь редактировать?
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Для редактирования не хватает прав!');
				$datestart = DateToMySQLDateTime2($datestart);
				$dateend = DateToMySQLDateTime2($dateend);
				try {
					$sql = <<<TXT
update contract
set num = :num, name = :name, comment = :comment, datestart = :datestart, dateend = :dateend, work = :work
where id = :id
TXT;
					DB::prepare($sql)->execute([
						':num' => $num,
						':name' => $name,
						':comment' => $comment,
						':datestart' => $datestart,
						':dateend' => $dateend,
						':work' => $work,
						':id' => $id
					]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу обновить данные по договору', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Для удаления не хватает прав!');
				try {
					switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							$sql = 'update contract set active = not active where id = :id';
							break;
						case 'pgsql':
							$sql = 'update contract set active = active # 1 where id = :id';
							break;
					}
					DB::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не смог пометить на удаление договор', 0, $ex);
				}
				break;
		}
	}

}
