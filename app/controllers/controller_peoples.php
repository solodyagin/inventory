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

class Controller_Peoples extends Controller {

	function index() {
		$user = User::getInstance();
		$cfg = Config::getInstance();
		$data['section'] = 'Справочники / Сотрудники';
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->generate('peoples/index', $cfg->theme, $data);
		} else {
			$this->view->generate('restricted', $cfg->theme, $data);
		}
	}

	/** Форма добавления сотрудника */
	function add() {
		$user = User::getInstance();
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->generate('peoples/add', '');
		} else {
			$data['section'] = 'Справочники / Сотрудники';
			$this->view->generate('restricted', '', $data);
		}
	}

	/** Форма редактирования сотрудника */
	function edit() {
		$user = User::getInstance();
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->generate('peoples/edit', '');
		} else {
			$data['section'] = 'Справочники / Сотрудники';
			$this->view->generate('restricted', '', $data);
		}
	}

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
		$filters = GetDef('filters');

		$flt = json_decode($filters, true);
		$cnt = is_array($flt['rules']) ? count($flt['rules']) : 0;
		$where = '';
		for ($i = 0; $i < $cnt; $i++) {
			$field = $flt['rules'][$i]['field'];
			$data = $flt['rules'][$i]['data'];
			$where .= "($field LIKE '%$data%')";
			if ($i < ($cnt - 1)) {
				$where .= ' AND ';
			}
		}
		if ($where != '') {
			$where = 'WHERE ' . $where;
		}

		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;

		try {
			$sql = 'SELECT COUNT(*) cnt FROM users';
			$row = DB::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список пользователей (1)', 0, $ex);
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
SELECT	u.`id`,
		o.`name` `orgname`,
		p.`fio`,
		u.`login`,
		u.`password`,
		u.`email`,
		u.`mode`,
		u.`active`
FROM	`users` u
	INNER JOIN `org` o
		ON o.`id` = u.`orgid`
	INNER JOIN `users_profile` p
		ON p.`usersid` = u.`id`
$where
ORDER BY	$sidx $sord
LIMIT		$start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
SELECT
	u.id,
	o.name orgname,
	p.fio,
	u.login,
	u.password,
	u.email,
	u.mode,
	u.active
FROM users u
	INNER JOIN org o ON o.id = u.orgid
	INNER JOIN users_profile p ON p.usersid = u.id
$where
ORDER BY $sidx $sord
OFFSET $start LIMIT $limit
TXT;
			}
			$arr = DB::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$mode = ($row['mode'] == '1') ? 'Да' : 'Нет';
				$ic = ($row['active'] == '1') ? 'fa-check-circle' : 'fa-ban';
				$responce->rows[$i]['cell'] = [
					"<i class=\"fa $ic\"></i>",
					$row['id'], $row['orgname'], $row['fio'], $row['login'], 'скрыто', $row['email'], $mode
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список пользователей (2)', 0, $ex);
		}
		jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = User::getInstance();
		$oper = PostDef('oper');
		$id = PostDef('id');
		$login = PostDef('login');
		$pass = PostDef('pass');
		$email = PostDef('email');
		$mode = PostDef('mode');
		switch ($oper) {
			case 'edit':
				/* Проверяем может ли пользователь редактировать? */
				(($user->mode == 1) || $user->TestRights([1])) or die('Недостаточно прав');
				$imode = ($mode == 'Да') ? '1' : '0';
				switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$ps = ($pass != 'скрыто') ? "`password`=SHA1(CONCAT(SHA1('$pass'), salt))," : '';
						break;
					case 'pgsql':
						$ps = ($pass != 'скрыто') ? "password=SHA1(CONCAT(SHA1('$pass'), salt::text))," : '';
						break;
				}
				$sql = "UPDATE users SET mode = :mode, login = :login, $ps email = :email WHERE id = :id";
				try {
					DB::prepare($sql)->execute([
						':mode' => $imode,
						':login' => $login,
						':email' => $email,
						':id' => $id
					]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу обновить данные по пользователю', 0, $ex);
				}
			case 'del':
				/* Проверяем может ли пользователь удалять? */
				($user->isAdmin() || $user->TestRights([1])) or die('Для удаления недостаточно прав');
				switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$sql = 'UPDATE users SET active = NOT active WHERE id = :id';
						break;
					case 'pgsql':
						$sql = 'UPDATE users SET active = active # 1 WHERE id = :id';
						break;
				}
				try {
					DB::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу пометить на удаление пользователя', 0, $ex);
				}
				break;
		}
	}

}
