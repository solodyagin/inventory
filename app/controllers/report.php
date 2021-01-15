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

class report extends controller {

	function index() {
		$user = user::getInstance();
		$data['section'] = 'Отчёты / Имущество';
		if ($user->isAdmin() || $user->testRights([1])) {
			$this->view->renderTemplate('report/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

	/** Для работы jqGrid */
	function list() {
		$req = request::getInstance();
		$page = $req->get('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = $req->get('rows');
		$sidx = $req->get('sidx', '1');
		$sord = $req->get('sord');
		$curuserid = $req->get('curuserid');
		$curplid = $req->get('curplid');
		$curorgid = $req->get('curorgid');
		$tpo = $req->get('tpo');
		$os = $req->get('os');
		$repair = $req->get('repair');
		$mode = $req->get('mode');
		$where = '';
		if ($curuserid != '-1') {
			$where .= " and eq.usersid = '$curuserid'";
		}
		if ($curplid != '-1') {
			$where .= " and eq.placesid = '$curplid'";
		}
		if ($curorgid != '-1') {
			$where .= " and eq.orgid = '$curorgid'";
		}
		if ($os == 'true') {
			$where .= " and eq.os = 1";
		}
		if ($repair == 'true') {
			$where .= " and eq.repair = 1";
		}
		if ($mode == 'true') {
			$where .= " and eq.mode = 1";
		}
		if ($tpo == '2') {
			$where .= " and eq.mode = 0 and eq.os = 0";
		}
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = <<<TXT
select count(eq.id) as cnt
from equipment eq
	inner join places pl on pl.id = eq.placesid
	inner join nome nm on nm.id = eq.nomeid
	inner join group_nome gr on gr.id = nm.groupid
	inner join users_profile u on u.usersid = eq.usersid
where eq.active = 1 $where
TXT;
			$row = db::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new dbexception('Не могу сформировать список по оргтехнике/помещениям/пользователю!(1)', 0, $ex);
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
	gr.name as grname,
	pl.name as plname,
	nm.name as namenome,
	nm.groupid as grpid,
	eq.id as eqid,
	eq.placesid as plid,
	eq.nomeid as nid,
	eq.buhname as bn,
	eq.cost as cs,
	eq.currentcost as curc,
	eq.invnum,
	eq.sernum,
	eq.shtrihkod,
	eq.mode,
	eq.os,
	eq.usersid,
	u.fio
from equipment eq
	inner join places pl on pl.id = eq.placesid
	inner join nome nm on nm.id = eq.nomeid
	inner join group_nome gr on gr.id = nm.groupid
	inner join users_profile u on u.usersid = eq.usersid
where eq.active = 1 $where
order by $sidx $sord
limit $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select
	gr.name as grname,
	pl.name as plname,
	nm.name as namenome,
	nm.groupid as grpid,
	eq.id as eqid,
	eq.placesid as plid,
	eq.nomeid as nid,
	eq.buhname as bn,
	eq.cost as cs,
	eq.currentcost as curc,
	eq.invnum,
	eq.sernum,
	eq.shtrihkod,
	eq.mode,
	eq.os,
	eq.usersid,
	u.fio
from equipment eq
	inner join places pl on pl.id = eq.placesid
	inner join nome nm on nm.id = eq.nomeid
	inner join group_nome gr on gr.id = nm.groupid
	inner join users_profile u on u.usersid = eq.usersid
where eq.active = 1 $where
order by $sidx $sord
offset $start limit $limit
TXT;
					break;
			}
			$arr = db::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['eqid'];
				$responce->rows[$i]['cell'] = [
					$row['eqid'], // Id
					$row['plname'], // Помещение
					$row['fio'], // Ответственный
					$row['grname'], // Группа номенклатуры
					$row['namenome'], // Наименование
					$row['invnum'], // Инв.№
					$row['sernum'], // Сер.№
					$row['shtrihkod'], // Штрихкод
					$row['mode'], // Списан
					$row['os'], // ОС
					$row['bn'], // Бух.имя
					//$row['cs'],
					//$row['curc'],
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу сформировать список по оргтехнике/помещениям/пользователю! (2)', 0, $ex);
		}
		utils::jsonExit($responce);
	}

}
