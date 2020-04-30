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

class Controller_Report extends Controller {

	function index() {
		$user = User::getInstance();
		$data['section'] = 'Отчёты / Имущество';
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->renderTemplate('report/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

	/** Для работы jqGrid */
	function list() {
		$page = GetDef('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = GetDef('rows');
		$sidx = GetDef('sidx', '1');
		$sord = GetDef('sord');
		$curuserid = GetDef('curuserid');
		$curplid = GetDef('curplid');
		$curorgid = GetDef('curorgid');
		$tpo = GetDef('tpo');
		$os = GetDef('os');
		$repair = GetDef('repair');
		$mode = GetDef('mode');
		$where = '';
		if ($curuserid != '-1') {
			$where .= " and equipment.usersid = '$curuserid'";
		}
		if ($curplid != '-1') {
			$where .= " and equipment.placesid = '$curplid'";
		}
		if ($curorgid != '-1') {
			$where .= " and equipment.orgid = '$curorgid'";
		}
		if ($os == 'true') {
			$where .= " and equipment.os = 1";
		}
		if ($repair == 'true') {
			$where .= " and equipment.repair = 1";
		}
		if ($mode == 'true') {
			$where .= " and equipment.mode = 1";
		}
		if ($tpo == '2') {
			$where .= " and equipment.mode = 0  and equipment.os = 0";
		}
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = <<<TXT
select
	places.name as plname,
	res.*
from places
	inner join (
		select
			name as namenome,
			eq.*
		from nome
			inner join (
				select
					equipment.id as eqid,
					equipment.placesid as plid,
					equipment.nomeid as nid,
					equipment.buhname as bn,
					equipment.cost as cs,
					equipment.currentcost as curc,
					equipment.invnum,
					equipment.sernum,
					equipment.shtrihkod,
					equipment.mode,
					equipment.os
				from equipment
				where equipment.active = 1 $where
			) as eq on nome.id = eq.nid
	) as res on places.id = res.plid
TXT;
			$rows = DB::prepare($sql)->execute()->fetchAll();
			$count = ($rows) ? count($rows) : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу сформировать список по оргтехнике/помещениям/пользователю!(1)', 0, $ex);
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
	name as grname,
	res2.*
from group_nome
	inner join (
		select
			places.name as plname,
            res.*
			from places
				inner join (
					select
						name as namenome,
						nome.groupid as grpid,
						eq.*
					from nome
						inner join (
							select
								equipment.id as eqid,
								equipment.placesid as plid,
								equipment.nomeid as nid,
								equipment.buhname as bn,
								equipment.cost as cs,
								equipment.currentcost as curc,
								equipment.invnum,
								equipment.sernum,
								equipment.shtrihkod,
								equipment.mode,
								equipment.os
							from equipment
							where equipment.active = 1 $where
						) as eq on nome.id = eq.nid
				) as res on places.id = res.plid
			) as res2 on group_nome.id = res2.grpid
order by $sidx $sord
limit $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select
	name as grname,
	res2.*
from group_nome
	inner join (
		select
			places.name as plname,
            res.*
			from places
				inner join (
					select
						name as namenome,
						nome.groupid as grpid,
						eq.*
					from nome
						inner join (
							select
								equipment.id as eqid,
								equipment.placesid as plid,
								equipment.nomeid as nid,
								equipment.buhname as bn,
								equipment.cost as cs,
								equipment.currentcost as curc,
								equipment.invnum,
								equipment.sernum,
								equipment.shtrihkod,
								equipment.mode,
								equipment.os
							from equipment
							where equipment.active = 1 $where
						) as eq on nome.id = eq.nid
				) as res on places.id = res.plid
			) as res2 on group_nome.id = res2.grpid
order by $sidx $sord
offset $start limit $limit
TXT;
					break;
			}
			$arr = DB::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['eqid'];
				$responce->rows[$i]['cell'] = [
					$row['eqid'], $row['plname'], $row['namenome'], $row['grname'], $row['invnum'], $row['sernum'], $row['shtrihkod'], $row['mode'], $row['os'], $row['bn'],
					$row['cs'], $row['curc']
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу сформировать список по оргтехнике/помещениям/пользователю! (2)', 0, $ex);
		}
		jsonExit($responce);
	}

}
