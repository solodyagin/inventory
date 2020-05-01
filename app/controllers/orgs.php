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

class orgs extends controller {

	function index() {
		$user = user::getInstance();
		$data['section'] = 'Справочники / Список организаций';
		if ($user->isAdmin() || $user->testRights([1])) {
			$this->view->renderTemplate('orgs/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

	/** Для работы jqGrid */
	function list() {
		$user = user::getInstance();
		// Проверяем: может ли пользователь просматривать?
		($user->isAdmin() || $user->testRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
		$req = request::getInstance();
		$page = $req->get('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = $req->get('rows');
		$sidx = $req->get('sidx', '1');
		$sord = $req->get('sord');
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		$sql = 'select count(*) as cnt from org';
		try {
			$row = db::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список организаций (1)', 0, $ex);
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
					$sql = "select id, name, active from org order by $sidx $sord limit $start, $limit";
					break;
				case 'pgsql':
					$sql = "select id, name, active from org order by $sidx $sord offset $start limit $limit";
					break;
			}
			$arr = db::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$ic = ($row['active'] == '1') ? 'fa-check-circle' : 'fa-ban';
				$responce->rows[$i]['cell'] = [
					"<i class=\"fas $ic\"></i>",
					$row['name']
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список организаций (2)', 0, $ex);
		}
		utils::jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = user::getInstance();
		$req = request::getInstance();
		$oper = $req->get('oper');
		$id = $req->get('id');
		$name = $req->get('name');
		switch ($oper) {
			case 'add':
				// Проверяем: может ли пользователь добавлять?
				($user->isAdmin() || $user->testRights([1, 4])) or die('Для добавления недостаточно прав');
				$sql = 'insert into org (name, active) values (:name, 1)';
				try {
					db::prepare($sql)->execute([':name' => $name]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу добавить организацию', 0, $ex);
				}
				break;
			case 'edit':
				// Проверяем: может ли пользователь редактировать?
				($user->isAdmin() || $user->testRights([1, 5])) or die('Для редактирования недостаточно прав');
				$sql = 'update org set name = :name where id = :id';
				try {
					db::prepare($sql)->execute([':name' => $name, ':id' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу обновить данные по организации', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->testRights([1, 6])) or die('Для удаления недостаточно прав');
				switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$sql = 'update org set active = not active where id = :id';
						break;
					case 'pgsql':
						$sql = 'update org set active = active # 1 where id = :id';
						break;
				}
				try {
					db::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу удалить организацию', 0, $ex);
				}
				break;
		}
	}

	function getlistorgs() {
		$user = user::getInstance();
		$req = request::getInstance();
		$addnone = $req->get('addnone');
		if ($user->isAdmin()) {
			echo '<select name="sogrsname" id="sorgsname">';
			if ($addnone == 'true') {
				echo '<option value="-1">не выбрано</option>';
			}
			try {
				switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$sql = 'select * from org where active = 1 order by binary(name)';
						break;
					case 'pgsql':
						$sql = 'select * from org where active = 1 order by name';
						break;
				}
				$arr = db::prepare($sql)->execute()->fetchAll();
				foreach ($arr as $row) {
					echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
				}
			} catch (PDOException $ex) {
				throw new dbexception('Не могу выбрать список организаций', 0, $ex);
			}
			echo '</select>';
		} else {
			echo 'Не достаточно прав!!!';
		}
	}

}
