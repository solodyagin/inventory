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
use core\config;
use core\request;
use core\user;
use core\db;
use core\dbexception;
use core\utils;

class equipment extends controller {

	function index() {
		$data['section'] = 'Журнал / Имущество';
		$user = user::getInstance();
		if ($user->isAdmin() || $user->testRights([1, 3, 4, 5, 6])) {
			$this->view->renderTemplate('equipment/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

	/** Для работы jqGrid */
	function list() {
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
		$cfg = config::getInstance();
		$sorgider = $req->get('sorgider', $cfg->defaultorgid);
		// Получаем наложенные поисковые фильтры
		$filters = $req->get('filters');
		$flt = json_decode($filters, true);
		$cnt = is_array($flt['rules']) ? count($flt['rules']) : 0;
		$where = '';
		switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
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
			$row = db::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new dbexception('Не получилось выбрать список оргтехники!', 0, $ex);
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
	equipment.dtendgar,
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
		select
			nome.groupid grnomeid,
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
select
	equipment.dtendgar,
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
		select
			nome.groupid grnomeid,
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
			$arr = db::prepare($sql)->execute()->fetchAll();
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
				$dtpost = utils::MySQLDateTimeToDateTime($row['datepost']);
				$dtendgar = utils::MySQLDateToDate($row['dtendgar']);
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
			throw new dbexception('Не получилось выбрать список оргтехники!', 0, $ex);
		}
		utils::jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = user::getInstance();
		$cfg = config::getInstance();
		$req = request::getInstance();
		$oper = $req->get('oper');
		$orgid = $cfg->defaultorgid;
		$name = $req->get('name');
		$comment = $req->get('comment');
		$os = $req->get('os');
		$tmcgo = $req->get('tmcgo');
		$mode = $req->get('mode');
		//$mapyet = $req->get('mapyet');
		$mapyet = $req->get('eqmapyet');
		$buhname = $req->get('buhname');
		$sernum = $req->get('sernum');
		$invnum = $req->get('invnum');
		$shtrihkod = $req->get('shtrihkod');
		$cost = $req->get('cost');
		$currentcost = $req->get('currentcost');
		$id = $req->get('id');
		switch ($oper) {
			case 'add':
				// Проверка: может ли пользователь добавлять?
				($user->isAdmin() || $user->testRights([1, 4])) or die('Недостаточно прав');
				try {
					$sql = 'insert into places (orgid, name, comment, active) values (:orgid, :name, :comment, 1)';
					db::prepare($sql)->execute([':orgid' => $orgid, ':name' => $name, ':comment' => $comment]);
				} catch (PDOException $ex) {
					throw new dbexception('Не смог добавить оргтехнику!', 0, $ex);
				}
				break;
			case 'edit':
				// Проверка: может ли пользователь редактировать?
				($user->isAdmin() || $user->testRights([1, 5])) or die('Недостаточно прав');
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
					db::prepare($sql)->execute([
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
					throw new dbexception('Не смог обновить оргтехнику!', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->testRights([1, 6])) or die('Недостаточно прав');
				try {
					switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
						case 'mysql':
							$sql = 'update equipment set active = not active where id = :id';
							break;
						case'pgsql':
							$sql = 'update equipment set active = active # 1 where id = :id';
							break;
					}
					db::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не смог пометить на удаление оргтехнику!', 0, $ex);
				}
				break;
		}
	}

	function getlistplaces() {
		$user = user::getInstance();
		$cfg = config::getInstance();
		$orgid = $cfg->defaultorgid;
		$req = request::getInstance();
		$placesid = $req->get('placesid');
		$addnone = $req->get('addnone');
		$oldopgroup = '';
		if ($user->isAdmin() || $user->testRights([1, 4, 5, 6])) {
			echo '<select name="splaces" id="splaces">';
			if ($addnone == 'true') {
				echo '<option value="-1">не выбрано</option>';
			}
			try {
				switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$sql = 'select * from places where orgid = :orgid and active = 1 order by binary(opgroup), binary(name)';
						break;
					case 'pgsql':
						$sql = 'select * from places where orgid = :orgid and active = 1 order by opgroup, name';
						break;
				}
				$arr = db::prepare($sql)->execute([':orgid' => $orgid])->fetchAll();
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
				throw new dbexception('Не могу выбрать список помещений!', 0, $ex);
			}
			echo '</optgroup>';
			echo '</select>';
		} else {
			echo 'Недостаточно прав';
		}
	}

	function getlistgroupname() {
		$user = user::getInstance();
		$req = request::getInstance();
		$addnone = $req->get('addnone');
		if ($user->isAdmin() || $user->testRights([1, 4, 5, 6])) {
			echo '<select name="sgroupname" id="sgroupname">';
			if ($addnone == 'true') {
				echo '<option value="-1">не выбрано</option>';
			}
			try {
				switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$sql = 'select * from group_nome where active = 1 order by binary(name)';
						break;
					case 'pgsql':
						$sql = 'select * from group_nome where active = 1 order by name';
						break;
				}
				$arr = db::prepare($sql)->execute()->fetchAll();
				foreach ($arr as $row) {
					echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
				}
			} catch (PDOException $ex) {
				throw new dbexception('Не могу выбрать список групп!', 0, $ex);
			}
			echo '</select>';
		} else {
			echo 'Недостаточно прав';
		}
	}

}
