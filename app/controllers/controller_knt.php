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

class Controller_Knt extends Controller {

	function index() {
		$user = User::getInstance();
		$data['section'] = 'Справочники / Контрагенты';
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->renderTemplate('knt/index', $data);
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
		$filters = GetDef('filters');
		$flt = json_decode($filters, true);
		$cnt = is_array($flt['rules']) ? count($flt['rules']) : 0;
		$where = '';
		switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
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
			$row = DB::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список контрагентов (1)', 0, $ex);
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
			$arr = DB::prepare($sql)->execute()->fetchAll();
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
			throw new DBException('Не могу выбрать список контрагентов', 0, $ex);
		}
		jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = User::getInstance();
		$oper = PostDef('oper');
		$id = PostDef('id');
		$name = PostDef('name');
		$inn = PostDef('inn');
		$kpp = PostDef('kpp');
		$bayer = PostDef('bayer');
		$supplier = PostDef('supplier');
		$erpcode = PostDef('erpcode');
		if (empty($erpcode)) {
			$erpcode = '0';
		}
		$dog = PostDef('dog');
		//$dog = ($dog == 'Yes') ? '1' : '0';
		$comment = PostDef('comment');
		switch ($oper) {
			case 'add':
				// Проверяем: может ли пользователь добавлять?
				($user->isAdmin() || $user->TestRights([1, 4])) or die('Недостаточно прав');
				try {
					switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
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
					DB::prepare($sql)->execute([
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
					throw new DBException('Не могу добавить контрагента', 0, $ex);
				}
				break;
			case 'edit':
				// Проверяем: может ли пользователь редактировать?
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Для редактирования не хватает прав!');
				$sql = <<<TXT
update knt
set name = :name, comment = :comment, inn = :inn, kpp = :kpp, bayer = :bayer, supplier = :supplier, dog = :dog, erpcode = :erpcode
where id = :id
TXT;
				try {
					DB::prepare($sql)->execute([
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
					throw new DBException('Не могу обновить данные по контрагенту', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем может ли пользователь удалять?
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Для удаления не хватает прав!');
				try {
					switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							$sql = 'update knt set active = not active where id = :id';
							break;
						case 'pgsql':
							$sql = 'update knt set active = active # 1 where id = :id';
							break;
					}
					DB::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не пометить на удаление контрагента', 0, $ex);
				}
				break;
		}
	}

}
