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

/* Запрещаем прямой вызов скрипта. */
defined('SITE_EXEC') or die('Доступ запрещён');

class Controller_Places extends Controller {

	function index() {
		$user = User::getInstance();
		$cfg = Config::getInstance();
		$data['section'] = 'Справочники / Помещения';
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->generate('places/index', $cfg->theme, $data);
		} else {
			$this->view->generate('restricted', $cfg->theme, $data);
		}
	}

	/** Для работы jqGrid */
	function list() {
		$user = User::getInstance();
		/* Проверяем может ли пользователь просматривать? */
		($user->isAdmin() || $user->TestRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
		$page = GetDef('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = GetDef('rows');
		$sidx = GetDef('sidx', '1');
		$sord = GetDef('sord');
		$orgid = GetDef('orgid');
		/* Готовим ответ */
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'SELECT COUNT(*) AS cnt FROM places WHERE orgid = :orgid';
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
			$sql = <<<TXT
SELECT id, opgroup, name, comment, active
FROM places
WHERE orgid = :orgid
ORDER BY :sidx :sord
LIMIT :start, :limit
TXT;
			$stmt = DB::prepare($sql);
			$stmt->bindValue(':orgid', $orgid, PDO::PARAM_INT);
			$stmt->bindValue(':sidx', $sidx, PDO::PARAM_STR);
			$stmt->bindValue(':sord', $sord, PDO::PARAM_STR);
			$stmt->bindValue(':start', $start, PDO::PARAM_INT);
			$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
			$arr = $stmt->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$ic = ($row['active'] == '1') ? 'fa-check-circle-o' : 'fa-ban';
				$responce->rows[$i]['cell'] = [
					"<i class=\"fa $ic\" aria-hidden=\"true\"></i>",
					$row['id'],
					$row['opgroup'],
					$row['name'],
					$row['comment']
				];
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
				/* Проверяем может ли пользователь добавлять? */
				($user->isAdmin() || $user->TestRights([1, 4])) or die('Недостаточно прав');
				try {
					$sql = <<<TXT
INSERT INTO places (id, orgid, opgroup, name, comment, active)
VALUES (null, :orgid, :opgroup, :name, :comment, 1)
TXT;
					DB::prepare($sql)->execute([
						':orgid' => $orgid,
						':opgroup' => $opgroup,
						':name' => $name,
						':comment' => $comment
					]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу добавить помещение', 0, $ex);
				}
				break;
			case 'edit':
				/* Проверяем может ли пользователь редактировать? */
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Недостаточно прав');
				try {
					$sql = 'UPDATE places SET opgroup = :opgroup, name = :name, comment = :comment WHERE id = :id';
					DB::prepare($sql)->execute([
						':opgroup' => $opgroup,
						':name' => $name,
						':comment' => $comment,
						':id' => $id
					]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу обновить данные по помещениям', 0, $ex);
				}
				break;
			case 'del':
				/* Проверяем может ли пользователь удалять? */
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Недостаточно прав');
				try {
					$sql = 'UPDATE places SET active = NOT active WHERE id = :id';
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
		/* Проверяем может ли пользователь просматривать? */
		($user->isAdmin() || $user->TestRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
		$page = GetDef('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = GetDef('rows');
		$sidx = GetDef('sidx', '1');
		$sord = GetDef('sord');
		$placesid = GetDef('placesid');
		/* Готовим ответ */
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'SELECT COUNT(*) AS cnt FROM places_users WHERE placesid = :placesid';
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
			$sql = <<<TXT
SELECT	places_users.id AS plid,
		placesid,
		userid,
		users_profile.fio AS name
FROM	places_users
	INNER JOIN users_profile
		ON users_profile.usersid = userid
WHERE	placesid = :placesid
ORDER BY :sidx :sord
LIMIT :start, :limit
TXT;
			$stmt = DB::prepare($sql);
			$stmt->bindValue(':placesid', $placesid, PDO::PARAM_INT);
			$stmt->bindValue(':sidx', $sidx, PDO::PARAM_STR);
			$stmt->bindValue(':sord', $sord, PDO::PARAM_STR);
			$stmt->bindValue(':start', $start, PDO::PARAM_INT);
			$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
			$arr = $stmt->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['plid'];
				$responce->rows[$i]['cell'] = [
					$row['plid'],
					$row['name']
				];
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
				/* Проверяем может ли пользователь добавлять? */
				($user->isAdmin() || $user->TestRights([1, 4])) or die('Для добавления недостаточно прав');
				if (($placesid == '') || ($name == '')) {
					die();
				}
				try {
					$sql = 'select count(*) cnt from places_users where placesid = :placesid and userid = :userid';
					$row = DB::prepare($sql)->execute([':placesid' => $placesid, ':userid' => $name])->fetch();
					$count = ($row) ? $row['cnt'] : 0;
					if ($count == 0) {
						$sql = 'INSERT INTO places_users (id, placesid, userid) VALUES (null, :placesid, :userid)';
						DB::prepare($sql)->execute([':placesid' => $placesid, ':userid' => $name]);
					}
				} catch (PDOException $ex) {
					throw new DBException('Не могу добавить помещение/пользователя', 0, $ex);
				}
				break;
			case 'edit':
				/* Проверяем может ли пользователь редактировать? */
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Для редактирования недостаточно прав');
				try {
					$sql = 'UPDATE places_users SET userid = :userid WHERE id = :id';
					DB::prepare($sql)->execute([':userid' => $name, ':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу обновить данные по помещениям/пользователям', 0, $ex);
				}
				break;
			case 'del':
				/* Проверяем может ли пользователь удалять? */
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Для удаления недостаточно прав');
				try {
					$sql = 'DELETE FROM places_users WHERE id = :id';
					DB::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу удалить помещение/пользователя', 0, $ex);
				}
				break;
		}
	}

}
