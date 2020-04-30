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

class Controller_Places extends Controller {

	function index() {
		$user = User::getInstance();
		$data['section'] = 'Справочники / Помещения';
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->renderTemplate('places/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

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
		$orgid = GetDef('orgid');
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'select count(*) as cnt from places where orgid = :orgid';
			$row = DB::prepare($sql)->execute([':orgid' => $orgid])->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список помещений (1)', 0, $ex);
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
select id, opgroup, name, comment, active
from places
where orgid = :orgid
order by $sidx $sord
limit :start, :limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select id, opgroup, name, comment, active
from places
where orgid = :orgid
order by $sidx $sord
offset :start limit :limit
TXT;
					break;
			}
			$stmt = DB::prepare($sql);
			$stmt->bindValue(':orgid', $orgid, PDO::PARAM_INT);
			//$stmt->bindValue(':sidx', $sidx, PDO::PARAM_STR);
			//$stmt->bindValue(':sord', $sord, PDO::PARAM_STR);
			$stmt->bindValue(':start', $start, PDO::PARAM_INT);
			$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
			$arr = $stmt->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$rowid = $row['id'];
				$responce->rows[$i]['id'] = $rowid;
				$ic = ($row['active'] == '1') ? 'fa-check-circle' : 'fa-ban';
				$responce->rows[$i]['cell'] = ["<i class=\"fas $ic\"></i>", $rowid, $row['opgroup'], $row['name'], $row['comment']];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список помещений (2)', 0, $ex);
		}
		jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = User::getInstance();
		$oper = PostDef('oper');
		$id = PostDef('id');
		$name = PostDef('name');
		$orgid = GetDef('orgid');
		$comment = PostDef('comment');
		$opgroup = PostDef('opgroup');
		switch ($oper) {
			case 'add':
				// Проверяем: может ли пользователь добавлять?
				($user->isAdmin() || $user->TestRights([1, 4])) or die('Недостаточно прав');
				try {
					$sql = <<<TXT
insert into places (orgid, opgroup, name, comment, active)
values (:orgid, :opgroup, :name, :comment, 1)
TXT;
					DB::prepare($sql)->execute([':orgid' => $orgid, ':opgroup' => $opgroup, ':name' => $name, ':comment' => $comment]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу добавить помещение', 0, $ex);
				}
				break;
			case 'edit':
				// Проверяем: может ли пользователь редактировать?
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Недостаточно прав');
				try {
					$sql = 'update places set opgroup = :opgroup, name = :name, comment = :comment where id = :id';
					DB::prepare($sql)->execute([':opgroup' => $opgroup, ':name' => $name, ':comment' => $comment, ':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу обновить данные по помещениям', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Недостаточно прав');
				try {
					switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							$sql = 'update places set active = not active where id = :id';
							break;
						case 'pgsql':
							$sql = 'update places set active = active # 1 where id = :id';
							break;
					}
					DB::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу пометить на удаление помещение', 0, $ex);
				}
				break;
		}
	}

	/** Для работы jqGrid */
	function listsub() {
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
		$placesid = GetDef('placesid', 0);
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'select count(*) as cnt from places_users where placesid = :placesid';
			$row = DB::prepare($sql)->execute([':placesid' => $placesid])->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список помещений/пользователей (1)', 0, $ex);
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
select
	places_users.id as plid,
	placesid,
	userid,
	users_profile.fio as name
from places_users
	inner join users_profile on users_profile.usersid = userid
where placesid = :placesid
order by $sidx $sord
limit :start, :limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select
	places_users.id as plid,
	placesid,
	userid,
	users_profile.fio as name
from places_users
	inner join users_profile on users_profile.usersid = userid
where placesid = :placesid
order by $sidx $sord
offset :start limit :limit
TXT;
					break;
			}
			$stmt = DB::prepare($sql);
			$stmt->bindValue(':placesid', $placesid, PDO::PARAM_INT);
			//$stmt->bindValue(':sidx', $sidx, PDO::PARAM_STR);
			//$stmt->bindValue(':sord', $sord, PDO::PARAM_STR);
			$stmt->bindValue(':start', $start, PDO::PARAM_INT);
			$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
			$arr = $stmt->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['plid'];
				$responce->rows[$i]['cell'] = [$row['plid'], $row['name']];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список помещений/пользователей (2)', 0, $ex);
		}
		jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function changesub() {
		$user = User::getInstance();
		$oper = PostDef('oper');
		$id = PostDef('id');
		$name = PostDef('name');
		$placesid = GetDef('placesid');
		switch ($oper) {
			case 'add':
				// Проверяем: может ли пользователь добавлять?
				($user->isAdmin() || $user->TestRights([1, 4])) or die('Для добавления недостаточно прав');
				if (($placesid == '') || ($name == '')) {
					die();
				}
				try {
					$sql = 'select count(*) cnt from places_users where placesid = :placesid and userid = :userid';
					$row = DB::prepare($sql)->execute([':placesid' => $placesid, ':userid' => $name])->fetch();
					$count = ($row) ? $row['cnt'] : 0;
					if ($count == 0) {
						$sql = 'insert into places_users (placesid, userid) values (:placesid, :userid)';
						DB::prepare($sql)->execute([':placesid' => $placesid, ':userid' => $name]);
					}
				} catch (PDOException $ex) {
					throw new DBException('Не могу добавить помещение/пользователя', 0, $ex);
				}
				break;
			case 'edit':
				// Проверяем: может ли пользователь редактировать?
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Для редактирования недостаточно прав');
				try {
					$sql = 'update places_users set userid = :userid where id = :id';
					DB::prepare($sql)->execute([':userid' => $name, ':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу обновить данные по помещениям/пользователям', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Для удаления недостаточно прав');
				try {
					$sql = 'delete from places_users where id = :id';
					DB::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу удалить помещение/пользователя', 0, $ex);
				}
				break;
		}
	}

}
