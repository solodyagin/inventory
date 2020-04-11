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

class Controller_Knt extends Controller {

	function index() {
		$user = User::getInstance();
		$cfg = Config::getInstance();
		$data['section'] = 'Справочники / Контрагенты';
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->generate('knt/index', $cfg->theme, $data);
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
		$filters = GetDef('filters');
		$flt = json_decode($filters, true);
		$cnt = is_array($flt['rules']) ? count($flt['rules']) : 0;
		$where = '';
		for ($i = 0; $i < $cnt; $i++) {
			$field = $flt['rules'][$i]['field'];
			$data = $flt['rules'][$i]['data'];
			if ($data != '-1') {
				$where = $where . "($field LIKE '%$data%')";
			} else {
				$where = $where . "($field LIKE '%%')";
			}
			if ($i < ($cnt - 1)) {
				$where = $where . ' AND ';
			}
		}
		if ($where != '') {
			$where = 'WHERE ' . $where;
		}
		/* Готовим ответ */
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'SELECT COUNT(*) cnt FROM knt';
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
SELECT id, name, inn, kpp, bayer, supplier, dog, erpcode, comment, active
FROM knt
$where
ORDER BY $sidx $sord
LIMIT $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
SELECT id, name, inn, kpp, bayer, supplier, dog, erpcode, comment, active
FROM knt
$where
ORDER BY $sidx $sord
OFFSET $start LIMIT $limit
TXT;
					break;
			}
			$arr = DB::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$ic = ($row['active'] == '1') ? 'fa-check-circle' : 'fa-ban';
				$responce->rows[$i]['cell'] = [
					"<i class=\"fas $ic\"></i>",
					$row['id'], $row['name'], $row['inn'], $row['kpp'], $row['bayer'], $row['supplier'], $row['dog'], $row['erpcode'], $row['comment']
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
				/* Проверяем может ли пользователь добавлять? */
				($user->isAdmin() || $user->TestRights([1, 4])) or die('Недостаточно прав');
				try {
					switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							$sql = <<<TXT
INSERT INTO knt (id, name, fullname, inn, kpp, bayer, supplier, dog, erpcode, comment, active)
VALUES (null, :name, '', :inn, :kpp, :bayer, :supplier, :dog, :erpcode, :comment, 1)
TXT;
							break;
						case 'pgsql':
							$sql = <<<TXT
INSERT INTO knt (name, fullname, inn, kpp, bayer, supplier, dog, erpcode, comment, active)
VALUES (:name, '', :inn, :kpp, :bayer, :supplier, :dog, :erpcode, :comment, 1)
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
				/* Проверяем может ли пользователь редактировать? */
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Для редактирования не хватает прав!');
				$sql = <<<TXT
UPDATE knt
SET name = :name, comment = :comment, inn = :inn, kpp = :kpp, bayer = :bayer, supplier = :supplier, dog = :dog, erpcode = :erpcode
WHERE id = :id
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
				/* Проверяем может ли пользователь удалять? */
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Для удаления не хватает прав!');
				try {
					switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							$sql = 'UPDATE knt SET active = NOT active WHERE id = :id';
							break;
						case 'pgsql':
							$sql = 'UPDATE knt SET active = active # 1 WHERE id = :id';
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
