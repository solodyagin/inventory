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

class Controller_Equipment extends Controller {

	function index() {
		$user = User::getInstance();
		$data['section'] = 'Журнал / Имущество';
		if ($user->isAdmin() || $user->TestRights([1, 3, 4, 5, 6])) {
			$this->view->renderTemplate('equipment/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

	/** Для работы jqGrid */
	function list() {
		$user = User::getInstance();
		($user->isAdmin() || $user->TestRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
		$page = GetDef('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = GetDef('rows');
		$sidx = GetDef('sidx', '1');
		$sord = GetDef('sord');
		$cfg = Config::getInstance();
		$sorgider = GetDef('sorgider', $cfg->defaultorgid);
		// Получаем наложенные поисковые фильтры
		$filters = GetDef('filters');
		$flt = json_decode($filters, true);
		$cnt = is_array($flt['rules']) ? count($flt['rules']) : 0;
		$where = '';
		switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
			case 'mysql':
				for ($i = 0; $i < $cnt; $i++) {
					$field = $flt['rules'][$i]['field'];
					if ($field == 'org.name') {
						$field = 'org.id';
					}
					$data = $flt['rules'][$i]['data'];
					if ($data != '-1') {
						if (($field == 'placesid') || ($field == 'getvendorandgroup.grnomeid')) {
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
					if ($field == 'org.name') {
						$field = 'org.id';
					}
					$data = $flt['rules'][$i]['data'];
					if ($data != '-1') {
						if (($field == 'placesid') || ($field == 'getvendorandgroup.grnomeid')) {
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
		if ($where == '') {
			$where = "where equipment.orgid='$sorgider'";
		} else {
			$where = "where $where and equipment.orgid='$sorgider'";
		}
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		$sql = 'select count(*) cnt from equipment';
		try {
			$row = DB::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не получилось выбрать список оргтехники!', 0, $ex);
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
select equipment.dtendgar,
  tmcgo,
  knt.name kntname,
  getvendorandgroup.grnomeid,
  equipment.id eqid,
  equipment.ip ip,
  equipment.orgid eqorgid,
  org.name orgname,
  getvendorandgroup.vendorname vname,
  getvendorandgroup.groupname grnome,
  places.name placesname,
  users_profile.fio fio,
  getvendorandgroup.nomename nomename,
  buhname,
  sernum,
  invnum,
  shtrihkod,
  datepost,
  cost,
  currentcost,
  os,
  equipment.mode eqmode,
  equipment.mapyet eqmapyet,
  equipment.comment eqcomment,
  equipment.active eqactive,
  equipment.repair eqrepair
from equipment
  inner join (
    select nome.groupid grnomeid,
      nome.id nomeid,
      vendor.name vendorname,
      group_nome.name groupname,
      nome.name nomename
    from nome
      inner join group_nome on nome.groupid = group_nome.id
      inner join vendor on nome.vendorid = vendor.id
  ) as getvendorandgroup on getvendorandgroup.nomeid = equipment.nomeid
  inner join org on org.id = equipment.orgid
  inner join places on places.id = equipment.placesid
  inner join users_profile on users_profile.usersid = equipment.usersid
  left join knt on knt.id = equipment.kntid
$where
order by $sidx $sord
limit $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select equipment.dtendgar,
  tmcgo,
  knt.name kntname,
  getvendorandgroup.grnomeid,
  equipment.id eqid,
  equipment.ip ip,
  equipment.orgid eqorgid,
  org.name orgname,
  getvendorandgroup.vendorname vname,
  getvendorandgroup.groupname grnome,
  places.name placesname,
  users_profile.fio fio,
  getvendorandgroup.nomename nomename,
  buhname,
  sernum,
  invnum,
  shtrihkod,
  datepost,
  cost,
  currentcost,
  os,
  equipment.mode eqmode,
  equipment.mapyet eqmapyet,
  equipment.comment eqcomment,
  equipment.active eqactive,
  equipment.repair eqrepair
from equipment
  inner join (
    select nome.groupid grnomeid,
      nome.id nomeid,
      vendor.name vendorname,
      group_nome.name groupname,
      nome.name nomename
    from nome
      inner join group_nome on nome.groupid = group_nome.id
      inner join vendor on nome.vendorid = vendor.id
  ) as getvendorandgroup on getvendorandgroup.nomeid = equipment.nomeid
  inner join org on org.id = equipment.orgid
  inner join places on places.id = equipment.placesid
  inner join users_profile on users_profile.usersid = equipment.usersid
  left join knt on knt.id = equipment.kntid
$where
order by $sidx $sord
offset $start limit $limit
TXT;
					break;
			}
			$arr = DB::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['eqid'];
				if ($row['eqactive'] == '1') {
					$active = '<i class="fas fa-check-circle"></i>';
				} else {
					$active = '<i class="fas fa-ban"></i>';
				}
				if ($row['eqrepair'] == '1') {
					$active = $active . '<i class="fas fa-exclamation-circle"></i>';
				}
				$os = ($row['os'] == '0') ? 'No' : 'Yes';
				$eqmode = ($row['eqmode'] == '0') ? 'No' : 'Yes';
				//$eqmapyet = ($row['eqmapyet'] == '0') ? 'No' : 'Yes';
				$dtpost = MySQLDateTimeToDateTime($row['datepost']);
				$dtendgar = MySQLDateToDate($row['dtendgar']);
				$tmcgo = ($row['tmcgo'] == '0') ? 'No' : 'Yes';
				$responce->rows[$i]['cell'] = [
					$active, $row['eqid'], $row['ip'], $row['placesname'],
					$row['nomename'], $row['grnome'], $tmcgo, $row['vname'],
					$row['buhname'], $row['sernum'], $row['invnum'], $row['shtrihkod'],
					$row['orgname'], $row['fio'], $dtpost, $row['cost'],
					$row['currentcost'], $os, $eqmode, $row['eqmapyet'],
					$row['eqcomment'], $row['eqrepair'], $dtendgar, $row['kntname']
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не получилось выбрать список оргтехники!', 0, $ex);
		}
		jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = User::getInstance();
		$cfg = Config::getInstance();
		$oper = PostDef('oper');
		$orgid = $cfg->defaultorgid;
		$name = PostDef('name');
		$comment = PostDef('comment');
		$os = PostDef('os');
		$tmcgo = PostDef('tmcgo');
		$mode = PostDef('mode');
		//$mapyet = PostDef('mapyet');
		$mapyet = PostDef('eqmapyet');
		$buhname = PostDef('buhname');
		$sernum = PostDef('sernum');
		$invnum = PostDef('invnum');
		$shtrihkod = PostDef('shtrihkod');
		$cost = PostDef('cost');
		$currentcost = PostDef('currentcost');
		$id = PostDef('id');
		switch ($oper) {
			case 'add':
				// Проверка: может ли пользователь добавлять?
				($user->isAdmin() || $user->TestRights([1, 4])) or die('Недостаточно прав');
				try {
					$sql = 'insert into places (orgid, name, comment, active) values (:orgid, :name, :comment, 1)';
					DB::prepare($sql)->execute([':orgid' => $orgid, ':name' => $name, ':comment' => $comment]);
				} catch (PDOException $ex) {
					throw new DBException('Не смог добавить оргтехнику!', 0, $ex);
				}
				break;
			case 'edit':
				// Проверка: может ли пользователь редактировать?
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Недостаточно прав');
				$os = ($os == 'Yes') ? 1 : 0;
				$tmcgo = ($tmcgo == 'Yes') ? 1 : 0;
				$mode = ($mode == 'Yes') ? 1 : 0;
				$mapyet = ($mapyet == 'Yes') ? 1 : 0;
				try {
					$sql = <<<TXT
update equipment
set buhname = :buhname, sernum = :sernum, invnum = :invnum, shtrihkod = :shtrihkod, cost = :cost,
	currentcost = :currentcost, os = :os, mode = :mode, mapyet = :mapyet, comment = :comment, tmcgo = :tmcgo
where id = :id
TXT;
					DB::prepare($sql)->execute([
						':buhname' => $buhname,
						':sernum' => $sernum,
						':invnum' => $invnum,
						':shtrihkod' => $shtrihkod,
						':cost' => $cost,
						':currentcost' => $currentcost,
						':os' => $os,
						':mode' => $mode,
						':mapyet' => $mapyet,
						':comment' => $comment,
						':tmcgo' => $tmcgo,
						':id' => $id
					]);
				} catch (PDOException $ex) {
					throw new DBException('Не смог обновить оргтехнику!', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Недостаточно прав');
				try {
					switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							$sql = 'update equipment set active = not active where id = :id';
							break;
						case'pgsql':
							$sql = 'update equipment set active = active # 1 where id = :id';
							break;
					}
					DB::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не смог пометить на удаление оргтехнику!', 0, $ex);
				}
				break;
		}
	}

	function getlistplaces() {
		$user = User::getInstance();
		$cfg = Config::getInstance();
		$orgid = $cfg->defaultorgid;
		$placesid = GetDef('placesid');
		$addnone = GetDef('addnone');
		$oldopgroup = '';
		if ($user->isAdmin() || $user->TestRights([1, 4, 5, 6])) {
			echo '<select name="splaces" id="splaces">';
			if ($addnone == 'true') {
				echo '<option value="-1">не выбрано</option>';
			}
			try {
				switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$sql = 'select * from places where orgid = :orgid and active = 1 order by binary(opgroup), binary(name)';
						break;
					case 'pgsql':
						$sql = 'select * from places where orgid = :orgid and active = 1 order by opgroup, name';
						break;
				}
				$arr = DB::prepare($sql)->execute([':orgid' => $orgid])->fetchAll();
				$flag = 0;
				foreach ($arr as $row) {
					$opgroup = $row['opgroup'];
					if ($opgroup != $oldopgroup) {
						if ($flag != 0) {
							echo '</optgroup>';
						}
						echo "<optgroup label=\"$opgroup\">";
						$flag = 1;
					}
					$sl = ($row['id'] == $placesid) ? 'selected' : '';
					echo "<option value=\"{$row['id']}\" $sl>{$row['name']}</option>";
					$oldopgroup = $opgroup;
				}
			} catch (PDOException $ex) {
				throw new DBException('Не могу выбрать список помещений!', 0, $ex);
			}
			echo '</optgroup>';
			echo '</select>';
		} else {
			echo 'Недостаточно прав';
		}
	}

	function getlistgroupname() {
		$user = User::getInstance();
		$addnone = GetDef('addnone');
		if ($user->isAdmin() || $user->TestRights([1, 4, 5, 6])) {
			echo '<select name="sgroupname" id="sgroupname">';
			if ($addnone == 'true') {
				echo '<option value="-1">не выбрано</option>';
			}
			try {
				switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$sql = 'select * from group_nome where active = 1 order by binary(name)';
						break;
					case 'pgsql':
						$sql = 'select * from group_nome where active = 1 order by name';
						break;
				}
				$arr = DB::prepare($sql)->execute()->fetchAll();
				foreach ($arr as $row) {
					echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
				}
			} catch (PDOException $ex) {
				throw new DBException('Не могу выбрать список групп!', 0, $ex);
			}
			echo '</select>';
		} else {
			echo 'Недостаточно прав';
		}
	}

}
