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

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

$page = GetDef('page', 1);
if ($page == 0) {
	$page = 1;
}
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$oper = PostDef('oper');
$curuserid = GetDef('curuserid');
$id = PostDef('id');

if ($oper == '') {
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
	inner join nome on (nome.groupid = group_nome.id)
	inner join equipment on (equipment.nomeid = nome.id)
	inner join places on (places.id = equipment.placesid)
where equipment.active = 1 and equipment.usersid = :userid
TXT;
		$row = DB::prepare($sql)->execute([':userid' => $curuserid])->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать сформировать список по оргтехнике/помещениям/пользователю (1)', 0, $ex);
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
	inner join nome on (nome.groupid = group_nome.id)
	inner join equipment on (equipment.nomeid = nome.id)
	inner join places on (places.id = equipment.placesid)
where equipment.active = 1 and equipment.usersid = :userid
order by :sidx :sord
limit :start, :limit
TXT;
		$stmt = DB::prepare($sql);
		$stmt->bindValue(':userid', $curuserid, PDO::PARAM_INT);
		$stmt->bindValue(':sidx', $sidx, PDO::PARAM_STR);
		$stmt->bindValue(':sord', $sord, PDO::PARAM_STR);
		$stmt->bindValue(':start', $start, PDO::PARAM_INT);
		$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
		$arr = $stmt->execute()->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['eqid'];
			$responce->rows[$i]['cell'] = [
				$row['eqid'],
				$row['plname'],
				$row['namenome'],
				$row['grname'],
				$row['invnum'],
				$row['sernum'],
				$row['shtrihkod'],
				$row['mode'],
				$row['os'],
				$row['cs'],
				$row['curc'],
				$row['bn']
			];
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать сформировать список по оргтехнике/помещениям/пользователю (2)', 0, $ex);
	}
	jsonExit($responce);
}
