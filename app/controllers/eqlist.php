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

class eqlist extends controller {

	function index() {
		$data['section'] = 'Инструменты / Оргтехника на моём рабочем месте';
		$user = user::getInstance();
		if ($user->isAdmin() || $user->testRights([1, 3, 4, 5, 6])) {
			$this->view->renderTemplate('eqlist/index', $data);
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
		$curuserid = $req->get('curuserid');
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = <<<TXT
select
	count(*) as cnt
from group_nome
	inner join nome on nome.groupid = group_nome.id
	inner join equipment on equipment.nomeid = nome.id
	inner join places_users on places_users.placesid = equipment.placesid
	inner join places on places.id = equipment.placesid
where equipment.active = 1 and places_users.userid = :userid
TXT;
			$row = db::prepare($sql)->execute([':userid' => $curuserid])->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать сформировать список по оргтехнике/помещениям/пользователю (1)', 0, $ex);
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
	group_nome.name as grname,
	places.name as plname,
	nome.name as namenome,
	nome.groupid as grpid,
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
from group_nome
	inner join nome on nome.groupid = group_nome.id
	inner join equipment on equipment.nomeid = nome.id
	inner join places_users on places_users.placesid = equipment.placesid
	inner join places on places.id = equipment.placesid
where equipment.active = 1 and places_users.userid = :userid
order by $sidx $sord
limit :start, :limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select
	group_nome.name as grname,
	places.name as plname,
	nome.name as namenome,
	nome.groupid as grpid,
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
from group_nome
	inner join nome on nome.groupid = group_nome.id
	inner join equipment on equipment.nomeid = nome.id
	inner join places_users on places_users.placesid = equipment.placesid
	inner join places on places.id = equipment.placesid
where equipment.active = 1 and places_users.userid = :userid
order by $sidx $sord
offset :start limit :limit
TXT;
					break;
			}
			$stmt = db::prepare($sql);
			$stmt->bindValue(':userid', $curuserid, PDO::PARAM_INT);
			//$stmt->bindValue(':sidx', $sidx, PDO::PARAM_STR);
			//$stmt->bindValue(':sord', $sord, PDO::PARAM_STR);
			$stmt->bindValue(':start', $start, PDO::PARAM_INT);
			$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
			$arr = $stmt->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['eqid'];
				$responce->rows[$i]['cell'] = [
					$row['eqid'], $row['plname'], $row['namenome'], $row['grname'],
					$row['invnum'], $row['sernum'], $row['shtrihkod'], $row['mode']
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать сформировать список по оргтехнике/помещениям/пользователю (2)', 0, $ex);
		}
		utils::jsonExit($responce);
	}

	/** Для работы jqGrid */
	function listmat() {
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
		$curuserid = $req->get('curuserid');
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = <<<TXT
select
	count(*) as cnt
from group_nome
	inner join nome on nome.groupid = group_nome.id
	inner join equipment on equipment.nomeid = nome.id
	inner join places on places.id = equipment.placesid
where equipment.active = 1 and equipment.usersid = :userid
TXT;
			$row = db::prepare($sql)->execute([':userid' => $curuserid])->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать сформировать список по оргтехнике/помещениям/пользователю (1)', 0, $ex);
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
	group_nome.name as grname,
	places.name as plname,
	nome.name as namenome,
	nome.groupid as grpid,
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
from group_nome
	inner join nome on nome.groupid = group_nome.id
	inner join equipment on equipment.nomeid = nome.id
	inner join places on places.id = equipment.placesid
where equipment.active = 1 and equipment.usersid = :userid
order by $sidx $sord
limit :start, :limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select
	group_nome.name as grname,
	places.name as plname,
	nome.name as namenome,
	nome.groupid as grpid,
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
from group_nome
	inner join nome on nome.groupid = group_nome.id
	inner join equipment on equipment.nomeid = nome.id
	inner join places on places.id = equipment.placesid
where equipment.active = 1 and equipment.usersid = :userid
order by $sidx $sord
offset :start limit :limit
TXT;
					break;
			}
			$stmt = db::prepare($sql);
			$stmt->bindValue(':userid', $curuserid, PDO::PARAM_INT);
			//$stmt->bindValue(':sidx', $sidx, PDO::PARAM_STR);
			//$stmt->bindValue(':sord', $sord, PDO::PARAM_STR);
			$stmt->bindValue(':start', $start, PDO::PARAM_INT);
			$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
			$arr = $stmt->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['eqid'];
				$responce->rows[$i]['cell'] = [
					$row['eqid'], $row['plname'], $row['namenome'], $row['grname'], $row['invnum'], $row['sernum'],
					$row['shtrihkod'], $row['mode'], $row['os'], $row['cs'], $row['curc'], $row['bn']
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать сформировать список по оргтехнике/помещениям/пользователю (2)', 0, $ex);
		}
		utils::jsonExit($responce);
	}

}
