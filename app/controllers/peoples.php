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

class peoples extends controller {

	function index() {
		$user = user::getInstance();
		$data['section'] = 'Справочники / Сотрудники';
		if ($user->isAdmin() || $user->testRights([1])) {
			$this->view->renderTemplate('peoples/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

	/** Форма добавления сотрудника */
	function add() {
		$user = user::getInstance();
		if ($user->isAdmin() || $user->testRights([1])) {
			$this->view->render('peoples/add');
		} else {
			$data['section'] = 'Справочники / Сотрудники';
			$this->view->render('restricted', $data);
		}
	}

	/** Форма редактирования сотрудника */
	function edit() {
		$user = user::getInstance();
		if ($user->isAdmin() || $user->testRights([1])) {
			$this->view->render('peoples/edit');
		} else {
			$data['section'] = 'Справочники / Сотрудники';
			$this->view->render('restricted', $data);
		}
	}

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
		$filters = $req->get('filters');
		$flt = json_decode($filters, true);
		$cnt = is_array($flt['rules']) ? count($flt['rules']) : 0;
		$where = '';
		for ($i = 0; $i < $cnt; $i++) {
			$field = $flt['rules'][$i]['field'];
			$data = $flt['rules'][$i]['data'];
			switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
				case 'mysql':
					$where .= "($field like '%$data%')";
					break;
				case 'pgsql':
					$where .= "($field::text ilike '%$data%')";
					break;
			}
			if ($i < ($cnt - 1)) {
				$where .= ' and ';
			}
		}
		if ($where != '') {
			$where = 'where ' . $where;
		}
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = <<<TXT
select
	count(*) cnt
from users u
	inner join org o on o.id = u.orgid
	inner join users_profile p on p.usersid = u.id
$where
TXT;
			$row = db::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список пользователей (1)', 0, $ex);
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
select
	u.id,
	o.name as orgname,
	p.fio,
	u.login,
	u.email,
	u.mode,
	u.active
from users u
	inner join org o on o.id = u.orgid
	inner join users_profile p on p.usersid = u.id
$where
order by $sidx $sord
limit $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select
	u.id,
	o.name orgname,
	p.fio,
	u.login,
	u.email,
	u.mode,
	u.active
from users u
	inner join org o on o.id = u.orgid
	inner join users_profile p on p.usersid = u.id
$where
order by $sidx $sord
offset $start limit $limit
TXT;
			}
			$arr = db::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$mode = ($row['mode'] == '1') ? 'Да' : 'Нет';
				$ic = ($row['active'] == '1') ? 'fa-check-circle' : 'fa-ban';
				$responce->rows[$i]['cell'] = [
					"<i class=\"fas $ic\"></i>",
					$row['id'], $row['orgname'], $row['fio'], $row['login'], $row['email'], $mode
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список пользователей (2)', 0, $ex);
		}
		utils::jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = user::getInstance();
		$req = request::getInstance();
		$oper = $req->get('oper');
		$id = $req->get('id');
		$login = $req->get('login');
		$email = $req->get('email');
		$mode = $req->get('mode');
		switch ($oper) {
			case 'edit':
				// Проверяем: может ли пользователь редактировать?
				($user->isAdmin() || $user->testRights([1])) or die('Недостаточно прав');
				$imode = ($mode == 'Да') ? '1' : '0';
				$sql = "update users set mode = :mode, login = :login, email = :email where id = :id";
				try {
					db::prepare($sql)->execute([':mode' => $imode, ':login' => $login, ':email' => $email, ':id' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу обновить данные по пользователю', 0, $ex);
				}
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->testRights([1])) or die('Для удаления недостаточно прав');
				try {
					switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							$sql = 'update users set active = not active where id = :id';
							break;
						case 'pgsql':
							$sql = 'update users set active = active # 1 where id = :id';
							break;
					}
					db::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу пометить на удаление пользователя', 0, $ex);
				}
				break;
		}
	}

	/**
	 * Роли: http://грибовы.рф/wiki/doku.php/основы:доступ:роли
	 */
	function getlistroles() {
		echo <<<TXT
<select name="rolesusers" id="rolesusers">
	<option value="1">Полный доступ</option>
	<!--<option value="2">Просмотр финансовых отчетов</option>-->
	<option value="3">Просмотр</option>
	<option value="4">Добавление</option>
	<option value="5">Редактирование</option>
	<option value="6">Удаление</option>
	<!--<option value="7">Отправка СМС</option>-->
	<!--<option value="8">Манипуляции с деньгами</option>-->
	<!--<option value="9">Редактирование карт</option>-->
</select>
TXT;
	}

}
