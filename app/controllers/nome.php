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

class nome extends controller {

	var $section = 'Справочники / Номенклатура';

	function index() {
		$data['section'] = $this->section;
		$user = user::getInstance();
		if ($user->isAdmin() || $user->testRights([1, 4, 5, 6])) {
			$this->view->renderTemplate('nome/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

	/** Форма добавления номенклатуры */
	function add() {
		$user = user::getInstance();
		if ($user->isAdmin() || $user->testRights([1])) {
			$this->view->render('nome/add');
		} else {
			$data['section'] = $this->section;
			$this->view->render('restricted', $data);
		}
	}

	/** Форма редактирования номенклатуры */
	function edit() {
		$user = user::getInstance();
		if ($user->isAdmin() || $user->testRights([1])) {
			$this->view->render('nome/edit');
		} else {
			$data['section'] = $this->section;
			$this->view->render('restricted', $data);
		}
	}

	/** Для работы jqGrid */
	function list() {
		// Проверяем: может ли пользователь просматривать?
		$user = user::getInstance();
		($user->isAdmin() || $user->testRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
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
			if ($field == 'nomeid') {
				$field = 'nome.id';
			}
			$data = $flt['rules'][$i]['data'];
			$where .= "($field like '%$data%')";
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
			$sql = 'select count(*) cnt from nome';
			$row = db::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список номенклатуры (1)', 0, $ex);
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
select nome.id nomeid,
	group_nome.name groupname,
	vendor.name vendorname,
	nome.name nomename,
	nome.active nomeactive
from nome
	inner join group_nome on group_nome.id = nome.groupid
	inner join vendor on nome.vendorid = vendor.id
$where
order by $sidx $sord
limit $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select nome.id nomeid,
	group_nome.name groupname,
	vendor.name vendorname,
	nome.name nomename,
	nome.active nomeactive
from nome
	inner join group_nome on group_nome.id = nome.groupid
	inner join vendor on nome.vendorid = vendor.id
$where
order by $sidx $sord
offset $start limit $limit
TXT;
					break;
			}
			$arr = db::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['nomeid'];
				$ic = ($row['nomeactive'] == '1') ? 'fa-check-circle' : 'fa-ban';
				$responce->rows[$i]['cell'] = [
					"<i class=\"fas $ic\"></i>",
					$row['nomeid'], $row['groupname'], $row['vendorname'], $row['nomename']
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список номенклатуры (2)', 0, $ex);
		}
		utils::jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = user::getInstance();
		$req = request::getInstance();
		$oper = $req->get('oper');
		$id = $req->get('id');
		$nomename = $req->get('nomename');
		switch ($oper) {
//			case 'add':
//				// Проверяем: может ли пользователь добавлять?
//				($user->isAdmin() || $user->testRights([1,4])) or die('Для добавления недостаточно прав');
//				$sql = 'insert into knt (id, name, comment, active) values (null, :name, :comment, 1)';
//				try {
//					db::prepare($sql)->execute([':name' => $nomename, ':comment' => $comment]);
//				} catch (PDOException $ex) {
//					throw new dbexception('Не могу добавить пользователя', 0, $ex);
//				}
//				break;
			case 'edit':
				// Проверяем: может ли пользователь редактировать?
				($user->isAdmin() || $user->testRights([1, 5])) or die('Для редактирования недостаточно прав');
				// Есть ли уже такая запись?
				try {
					$sql = 'select count(*) cnt from nome where name = :name';
					$row = db::prepare($sql)->execute([':name' => $nomename])->fetch();
					$count = ($row) ? $row['cnt'] : 0;
				} catch (PDOException $ex) {
					throw new dbexception('Не могу обновить данные по номенклатуре (1)', 0, $ex);
				}
				if ($count == 0) {
					$sql = 'update nome set name = :name where id = :id';
					try {
						db::prepare($sql)->execute([':name' => $nomename, ':id' => $id]);
					} catch (PDOException $ex) {
						throw new dbexception('Не могу обновить данные по номенклатуре (2)', 0, $ex);
					}
				}
				break;
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->testRights([1, 6])) or die('Для удаления недостаточно прав');
				switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$sql = 'update nome set active = not active where id = :id';
						break;
					case 'pgsql':
						$sql = 'update nome set active = active # 1 where id = :id';
						break;
				}
				try {
					db::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу пометить на удаление номенклатуру', 0, $ex);
				}
				break;
		}
	}

}
