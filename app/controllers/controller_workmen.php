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

class Controller_Workmen extends Controller {

	function index() {
		$user = User::getInstance();
		$data['section'] = 'Инструменты / Менеджер по обслуживанию';
		if ($user->isAdmin() || $user->TestRights([1, 3, 4, 5, 6])) {
			$this->view->renderTemplate('workmen/index', $data);
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
		// Получаем наложенные поисковые фильтры
		$flt = json_decode($filters, true);
		$cnt = is_array($flt['rules']) ? count($flt['rules']) : 0;
		$where = '';
		switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
			case 'mysql':
				for ($i = 0; $i < $cnt; $i++) {
					$field = $flt['rules'][$i]['field'];
					if ($field == 'groupnomename') {
						$field = 'group_nome.id';
					}
					if ($field == 'idnome') {
						$field = 'equipment.id';
					}
					if ($field == 'nomename') {
						$field = 'nome.name';
					}
					if ($field == 'orgname') {
						$field = 'equipment.orgid';
					}
					$data = $flt['rules'][$i]['data'];
					if ($data != '-1') {
						if (($field == 'group_nome.id') || ($field == 'equipment.orgid')) {
							$where .= "($field = '$data')";
						} else {
							$where .= "($field like '%$data%')";
						}
					} else {
						$where .= "($field like '%%')";
					}
					if ($i < ($cnt - 1)) {
						$where .= ' and ';
					}
				}
				break;
			case 'pgsql':
				for ($i = 0; $i < $cnt; $i++) {
					$field = $flt['rules'][$i]['field'];
					if ($field == 'groupnomename') {
						$field = 'group_nome.id';
					}
					if ($field == 'idnome') {
						$field = 'equipment.id';
					}
					if ($field == 'nomename') {
						$field = 'nome.name';
					}
					if ($field == 'orgname') {
						$field = 'equipment.orgid';
					}
					$data = $flt['rules'][$i]['data'];
					if ($data != '-1') {
						if (($field == 'group_nome.id') || ($field == 'equipment.orgid')) {
							$where .= "($field = '$data')";
						} else {
							$where .= "($field::text ilike '%$data%')";
						}
					} else {
						$where .= "($field::text ilike '%%')";
					}
					if ($i < ($cnt - 1)) {
						$where .= ' and ';
					}
				}
				break;
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
			$sql = 'select count(*) as cnt from equipment';
			$row = DB::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список имущества (1)', 0, $ex);
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
	equipment.orgid,
	equipment.invnum as invnum,
	equipment.id as id,
	equipment.repair,
	org.name as orgname,
	equipment.id as idnome,
	nome.groupid as groupid,
	nome.name as nomename,
	users_profile.fio as fio,
	places.name as placename,
	group_nome.name as groupnomename,
	group_nome.id as grid
from equipment
	inner join org on equipment.orgid = org.id
	inner join nome on equipment.nomeid = nome.id
	inner join users_profile on equipment.usersid = users_profile.usersid
	inner join places on equipment.placesid = places.id
	inner join group_nome on group_nome.id = groupid
$where
order by $sidx $sord
limit $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select
	equipment.orgid,
	equipment.invnum as invnum,
	equipment.id as id,
	equipment.repair,
	org.name as orgname,
	equipment.id as idnome,
	nome.groupid as groupid,
	nome.name as nomename,
	users_profile.fio as fio,
	places.name as placename,
	group_nome.name as groupnomename,
	group_nome.id as grid
from equipment
	inner join org on equipment.orgid = org.id
	inner join nome on equipment.nomeid = nome.id
	inner join users_profile on equipment.usersid = users_profile.usersid
	inner join places on equipment.placesid = places.id
	inner join group_nome on group_nome.id = groupid
$where
order by $sidx $sord
offset $start limit $limit
TXT;
					break;
			}
			$arr = DB::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				switch ($row['repair']) {
					case 0: $st = 'Работает';
						break;
					case 1: $st = 'В сервисе';
						break;
					case 2: $st = 'Есть заявка';
						break;
					case 3: $st = 'Списать';
						break;
				}
				$eqid = $row['id'];

				switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$sql = <<<TXT
select count(id) as cntmonth
from repair
where dt > date_add(now(), interval - 31 day)
	and eqid = :eqid
TXT;
						break;
					case 'pgsql':
						$sql = <<<TXT
select count(id) as cntmonth
from repair
where dt > current_date - interval '31 day'
	and eqid = :eqid
TXT;
						break;
				}
				$row2 = DB::prepare($sql)->execute([':eqid' => $eqid])->fetch();
				if ($row2) {
					$cnm = $row2['cntmonth'];
				}

				switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$sql = <<<TXT
select count(id) as cntyear
from repair
where dt > date_add(now(), interval - 365 day)
	and eqid = :eqid
TXT;
						break;
					case 'pgsql':
						$sql = <<<TXT
select count(id) as cntyear
from repair
where dt > current_date - interval '365 day'
	and eqid = :eqid
TXT;
						break;
				}
				$row2 = DB::prepare($sql)->execute([':eqid' => $eqid])->fetch();
				if ($row2) {
					$cny = $row2['cntyear'];
				}
				$responce->rows[$i]['cell'] = array($st, $row['orgname'], $row['placename'],
					$row['groupnomename'], $row['id'], $row['invnum'], $row['nomename'],
					$row['fio'], $cnm, $cny
				);
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список имущества (2)', 0, $ex);
		}
		jsonExit($responce);
	}

}
