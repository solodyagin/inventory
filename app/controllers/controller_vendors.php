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

class Controller_Vendors extends Controller {

	function index() {
		$user = User::getInstance();
		$cfg = Config::getInstance();
		$data['section'] = 'Справочники / Производители';
		if ($user->isAdmin() || $user->TestRights([1, 3, 4, 5, 6])) {
			$this->view->generate('vendors/index', $cfg->theme, $data);
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
		/* Готовим ответ */
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'SELECT COUNT(*) cnt FROM vendor';
			$row = DB::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список производителей (1)', 0, $ex);
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
					$sql = "SELECT id, name, comment, active FROM vendor ORDER BY $sidx $sord LIMIT $start, $limit";
					break;
				case 'pgsql':
					$sql = "SELECT id, name, comment, active FROM vendor ORDER BY $sidx $sord OFFSET $start LIMIT $limit";
					break;
			}
			$arr = DB::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$ic = ($row['active'] == '1') ? 'fa-check-circle' : 'fa-ban';
				$responce->rows[$i]['cell'] = [
					"<i class=\"fas $ic\"></i>",
					$row['id'],
					$row['name'],
					$row['comment']
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список производителей (2)', 0, $ex);
		}
		jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = User::getInstance();
		$oper = PostDef('oper');
		$id = PostDef('id');
		$name = PostDef('name');
		$comment = PostDef('comment');
		switch ($oper) {
			case 'add':
				/* Проверяем может ли пользователь добавлять? */
				($user->isAdmin() || $user->TestRights([1, 4])) or die('Для добавления недостаточно прав');
				try {
					$sql = 'INSERT INTO vendor (name, comment, active) VALUES (:name, :comment, 1)';
					DB::prepare($sql)->execute([
						':name' => $name,
						':comment' => $comment
					]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу добавить производителя', 0, $ex);
				}
				break;
			case 'edit':
				/* Проверяем может ли пользователь редактировать? */
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Для редактирования недостаточно прав');
				try {
					$sql = 'UPDATE vendor SET name = :name, comment = :comment WHERE id = :id';
					DB::prepare($sql)->execute([
						':name' => $name,
						':comment' => $comment,
						':id' => $id
					]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу обновить данные по производителю', 0, $ex);
				}
				break;
			case 'del':
				/* Проверяем может ли пользователь удалять? */
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Для удаления недостаточно прав');
				try {
					switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							$sql = 'UPDATE vendor SET active = NOT active WHERE id = :id';
							break;
						case 'pgsql':
							$sql = 'UPDATE vendor SET active = active # 1 WHERE id = :id';
							break;
					}
					DB::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу пометить на удаление производителя', 0, $ex);
				}
				break;
		}
	}

}
