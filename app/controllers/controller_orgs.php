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

class Controller_Orgs extends Controller {

	function index() {
		$user = User::getInstance();
		$data['section'] = 'Справочники / Список организаций';
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->renderTemplate('orgs/index', $data);
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
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		$sql = 'select count(*) as cnt from org';
		try {
			$row = DB::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список организаций (1)', 0, $ex);
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
					$sql = "select id, name, active from org order by $sidx $sord limit $start, $limit";
					break;
				case 'pgsql':
					$sql = "select id, name, active from org order by $sidx $sord offset $start limit $limit";
					break;
			}
			$arr = DB::prepare($sql)->execute()->fetchAll();
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
			throw new DBException('Не могу выбрать список организаций (2)', 0, $ex);
		}
		jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = User::getInstance();
		$oper = PostDef('oper');
		$id = PostDef('id');
		$name = PostDef('name');
		switch ($oper) {
			case 'add':
				// Проверяем: может ли пользователь добавлять?
				($user->isAdmin() || $user->TestRights([1, 4])) or die('Для добавления недостаточно прав');
				$sql = 'insert into org (name, active) values (:name, 1)';
				try {
					DB::prepare($sql)->execute([':name' => $name]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу добавить организацию', 0, $ex);
				}
				break;
			case 'edit':
				// Проверяем: может ли пользователь редактировать?
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Для редактирования недостаточно прав');
				$sql = 'update org set name = :name where id = :id';
				try {
					DB::prepare($sql)->execute([':name' => $name, ':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу обновить данные по организации', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Для удаления недостаточно прав');
				switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$sql = 'update org set active = not active where id = :id';
						break;
					case 'pgsql':
						$sql = 'update org set active = active # 1 where id = :id';
						break;
				}
				try {
					DB::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу удалить организацию', 0, $ex);
				}
				break;
		}
	}

	function getlistorgs() {
		$user = User::getInstance();
		$addnone = GetDef('addnone');
		if ($user->isAdmin()) {
			echo '<select name="sogrsname" id="sorgsname">';
			if ($addnone == 'true') {
				echo '<option value="-1">не выбрано</option>';
			}
			try {
				switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$sql = 'select * from org where active = 1 order by binary(name)';
						break;
					case 'pgsql':
						$sql = 'select * from org where active = 1 order by name';
						break;
				}
				$arr = DB::prepare($sql)->execute()->fetchAll();
				foreach ($arr as $row) {
					echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
				}
			} catch (PDOException $ex) {
				throw new DBException('Не могу выбрать список организаций', 0, $ex);
			}
			echo '</select>';
		} else {
			echo 'Не достаточно прав!!!';
		}
	}

}
