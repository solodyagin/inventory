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

class moveinfo extends controller {

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
		$eqid = $req->get('eqid');
		// Если не задано ТМЦ по которому показываем перемещения, то тогда просто листаем последние
		if ($eqid == '') {
			$where = '';
		} else {
			$where = "where move.eqid = '$eqid'";
		}
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			//$sql = 'select count(*) cnt from move';
			switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
				case 'mysql':
					$sql = <<<TXT
select
	count(*) cnt
from move
	inner join (
		select move.id,
			move.eqid,
			equipment.nomeid,
			move.dt as dt,
			org.name as orgname1,
			places.name as place1,
			users_profile.fio as user1
		from move
			inner join org on org.id = orgidfrom
			inner join places on places.id = placesidfrom
		inner join users_profile on users_profile.usersid = useridfrom
		inner join equipment on equipment.id = eqid
	) as mv on move.id = mv.id
	inner join org on org.id = move.orgidto
	inner join places on places.id = placesidto
	inner join users_profile on users_profile.usersid = useridto
	inner join nome on nome.id = mv.nomeid
$where
TXT;
				case 'pgsql':
					$sql = <<<TXT
select
	count(*) cnt
from move
	inner join (
		select
			move.id,
			move.eqid,
			equipment.nomeid,
			move.dt as dt,
			org.name as orgname1,
			places.name as place1,
			users_profile.fio as user1
		from move
			inner join org on org.id = orgidfrom
			inner join places on places.id = placesidfrom
		inner join users_profile on users_profile.usersid = useridfrom
		inner join equipment on equipment.id = eqid
	) as mv on move.id = mv.id
	inner join org on org.id = move.orgidto
	inner join places on places.id = placesidto
	inner join users_profile on users_profile.usersid = useridto
	inner join nome on nome.id = mv.nomeid
$where
TXT;
					break;
			}
			$row = db::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new dbexception($ex->getMessage());
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
	mv.id,
	mv.eqid,
	nome.name,
	mv.nomeid,
	mv.dt,
	mv.orgname1,
	org.name as orgname2,
	mv.place1,
	places.name as place2,
	mv.user1,
	users_profile.fio as user2,
	move.comment as comment
from move
	inner join (
		select move.id,
			move.eqid,
			equipment.nomeid,
			move.dt as dt,
			org.name as orgname1,
			places.name as place1,
			users_profile.fio as user1
		from move
			inner join org on org.id = orgidfrom
			inner join places on places.id = placesidfrom
		inner join users_profile on users_profile.usersid = useridfrom
		inner join equipment on equipment.id = eqid
	) as mv on move.id = mv.id
	inner join org on org.id = move.orgidto
	inner join places on places.id = placesidto
	inner join users_profile on users_profile.usersid = useridto
	inner join nome on nome.id = mv.nomeid
$where
order by $sidx $sord
limit $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select
	mv.id,
	mv.eqid,
	nome.name,
	mv.nomeid,
	-- to_char(mv.dt, 'dd.MM.yyyy HH24:mi:ss') as dt,
	mv.dt::timestamp(0) as dt,
	mv.orgname1,
	org.name as orgname2,
	mv.place1,
	places.name as place2,
	mv.user1,
	users_profile.fio as user2,
	move.comment as comment
from move
	inner join (
		select
			move.id,
			move.eqid,
			equipment.nomeid,
			move.dt as dt,
			org.name as orgname1,
			places.name as place1,
			users_profile.fio as user1
		from move
			inner join org on org.id = orgidfrom
			inner join places on places.id = placesidfrom
		inner join users_profile on users_profile.usersid = useridfrom
		inner join equipment on equipment.id = eqid
	) as mv on move.id = mv.id
	inner join org on org.id = move.orgidto
	inner join places on places.id = placesidto
	inner join users_profile on users_profile.usersid = useridto
	inner join nome on nome.id = mv.nomeid
$where
order by $sidx $sord
offset $start limit $limit
TXT;
					break;
			}
			$i = 0;
			$arr = db::prepare($sql)->execute()->fetchAll();
			foreach ($arr as $row) {
				$rowid = $row['id'];
				$responce->rows[$i]['id'] = $rowid;
				$responce->rows[$i]['cell'] = [
					$rowid, $row['dt'],
					$row['orgname1'], $row['place1'], $row['user1'], $row['orgname2'],
					$row['place2'], $row['user2'], $row['name'], $row['comment']
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список перемещений', 0, $ex);
		}
		utils::jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = user::getInstance();
		$req = request::getInstance();
		$oper = $req->get('oper');
		$comment = $req->get('comment');
		$id = $req->get('id');
		switch ($oper) {
			case'edit':
				// Проверяем: может ли пользователь редактировать?
				($user->isAdmin() || $user->testRights([1, 5])) or die('Недостаточно прав');
				try {
					$sql = 'update move set comment = :comment where id = :id';
					db::prepare($sql)->execute([':comment' => $comment, ':id' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу обновить комментарий', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->testRights([1, 6])) or die('Недостаточно прав');
				try {
					$sql = 'delete from move where id = :id';
					db::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу удалить запись о перемещении', 0, $ex);
				}
				break;
		}
	}

}
