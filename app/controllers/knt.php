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

class knt extends controller {

	function index() {
		$user = user::getInstance();
		$data['section'] = 'Справочники / Контрагенты';
		if ($user->isAdmin() || $user->testRights([1])) {
			$this->view->renderTemplate('knt/index', $data);
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
		$filters = $req->get('filters');
		$flt = json_decode($filters, true);
		$cnt = is_array($flt['rules']) ? count($flt['rules']) : 0;
		$where = '';
		switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
			case 'mysql':
				for ($i = 0; $i < $cnt; $i++) {
					$field = $flt['rules'][$i]['field'];
					$data = $flt['rules'][$i]['data'];
					if ($data != '-1') {
						$where .= "($field like '%$data%')";
					} else {
						$where .= "($field like '%%')";
					}
					if ($i < ($cnt - 1)) {
						$where .= ' and ';
					}
				}
				if ($where != '') {
					$where = 'where ' . $where;
				}
				break;
			case 'pgsql':
				for ($i = 0; $i < $cnt; $i++) {
					$field = $flt['rules'][$i]['field'];
					$data = $flt['rules'][$i]['data'];
					if ($data != '-1') {
						$where .= "($field::text ilike '%$data%')";
					} else {
						$where .= "($field::text ilike '%%')";
					}
					if ($i < ($cnt - 1)) {
						$where .= ' and ';
					}
				}
				if ($where != '') {
					$where = 'where ' . $where;
				}
				break;
		}
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'select count(*) cnt from knt';
			$row = db::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список контрагентов (1)', 0, $ex);
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
	id, name, inn, kpp, bayer, supplier, dog, erpcode, comment, active
from knt
$where
order by $sidx $sord
limit $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select
	id, name, inn, kpp, bayer, supplier, dog, erpcode, comment, active
from knt
$where
order by $sidx $sord
offset $start limit $limit
TXT;
					break;
			}
			$arr = db::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$rowid = $row['id'];
				$responce->rows[$i]['id'] = $rowid;
				$ic = ($row['active'] == '1') ? 'fa-check-circle' : 'fa-ban';
				$responce->rows[$i]['cell'] = [
					"<i class=\"fas $ic\"></i>",
					$rowid, $row['name'], $row['inn'], $row['kpp'], $row['bayer'], $row['supplier'], $row['dog'], $row['erpcode'], $row['comment']
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список контрагентов', 0, $ex);
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
		$inn = $req->get('inn');
		$kpp = $req->get('kpp');
		$bayer = $req->get('bayer');
		$supplier = $req->get('supplier');
		$erpcode = $req->get('erpcode');
		if (empty($erpcode)) {
			$erpcode = '0';
		}
		$dog = $req->get('dog');
		//$dog = ($dog == 'Yes') ? '1' : '0';
		$comment = $req->get('comment');
		switch ($oper) {
			case 'add':
				// Проверяем: может ли пользователь добавлять?
				($user->isAdmin() || $user->testRights([1, 4])) or die('Недостаточно прав');
				try {
					switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							$sql = <<<TXT
insert into knt (id, name, fullname, inn, kpp, bayer, supplier, dog, erpcode, comment, active)
values (null, :name, '', :inn, :kpp, :bayer, :supplier, :dog, :erpcode, :comment, 1)
TXT;
							break;
						case 'pgsql':
							$sql = <<<TXT
insert into knt (name, fullname, inn, kpp, bayer, supplier, dog, erpcode, comment, active)
values (:name, '', :inn, :kpp, :bayer, :supplier, :dog, :erpcode, :comment, 1)
TXT;
							break;
					}
					db::prepare($sql)->execute([
						':name' => $name,
						':inn' => $inn,
						':kpp' => $kpp,
						':bayer' => $bayer,
						':supplier' => $supplier,
						':dog' => $dog,
						':erpcode' => $erpcode,
						':comment' => $comment
					]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу добавить контрагента', 0, $ex);
				}
				break;
			case 'edit':
				// Проверяем: может ли пользователь редактировать?
				($user->isAdmin() || $user->testRights([1, 5])) or die('Для редактирования не хватает прав!');
				$sql = <<<TXT
update knt
set name = :name, comment = :comment, inn = :inn, kpp = :kpp, bayer = :bayer, supplier = :supplier, dog = :dog, erpcode = :erpcode
where id = :id
TXT;
				try {
					db::prepare($sql)->execute([
						':name' => $name,
						':comment' => $comment,
						':inn' => $inn,
						':kpp' => $kpp,
						':bayer' => $bayer,
						':supplier' => $supplier,
						':dog' => $dog,
						':erpcode' => $erpcode,
						':id' => $id
					]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу обновить данные по контрагенту', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем может ли пользователь удалять?
				($user->isAdmin() || $user->testRights([1, 6])) or die('Для удаления не хватает прав!');
				try {
					switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							$sql = 'update knt set active = not active where id = :id';
							break;
						case 'pgsql':
							$sql = 'update knt set active = active # 1 where id = :id';
							break;
					}
					db::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не пометить на удаление контрагента', 0, $ex);
				}
				break;
		}
	}

}
