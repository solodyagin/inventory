<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$page = GetDef('page', 1);
if ($page == 0) {
	$page = 1;
}
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$oper = PostDef('oper');
$curuserid = GetDef('curuserid');
$curplid = GetDef('curplid');
$curorgid = GetDef('curorgid');
$tpo = GetDef('tpo');
$os = GetDef('os');
$repair = GetDef('repair');
$mode = GetDef('mode');
$id = PostDef('id');

$where = '';
if ($curuserid != '-1') {
	$where .= " AND equipment.usersid = '$curuserid'";
}
if ($curplid != '-1') {
	$where .= " AND equipment.placesid = '$curplid'";
}
if ($curorgid != '-1') {
	$where .= " AND equipment.orgid = '$curorgid'";
}
if ($os == 'true') {
	$where .= " AND equipment.os = 1";
}
if ($repair == 'true') {
	$where .= " AND equipment.repair = 1";
}
if ($mode == 'true') {
	$where .= " AND equipment.mode = 1";
}
if ($tpo == '2') {
	$where .= " AND equipment.mode = 0  AND equipment.os = 0";
}

if ($oper == '') {
	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$sql = <<<TXT
SELECT     COUNT(*)    AS cnt,
           places.name AS plname,
           res.*
FROM       places
INNER JOIN
           (
                      SELECT     name AS namenome,
                                 eq . *
                      FROM       nome
                      INNER JOIN
                                 (
                                        SELECT equipment.id          AS eqid,
                                               equipment.placesid    AS plid,
                                               equipment.nomeid      AS nid,
                                               equipment.buhname     AS bn,
                                               equipment.cost        AS cs,
                                               equipment.currentcost AS curc,
                                               equipment.invnum,
                                               equipment.sernum,
                                               equipment.shtrihkod,
                                               equipment.mode,
                                               equipment.os
                                        FROM   equipment
                                        WHERE  equipment.active = 1 $where) AS eq
                      ON         nome.id = eq.nid) AS res
ON         places.id = res.plid
TXT;
	try {
		$row = DB::prepare($sql)->execute()->fetch();
		$count = ($row) ? $row['cnt'] : 0;
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

	$sql = <<<TXT
SELECT     name AS grname,
           res2.*
FROM       group_nome
INNER JOIN
           (
                      SELECT     places.name AS plname,
                                 res.*
                      FROM       places
                      INNER JOIN
                                 (
                                            SELECT     name         AS namenome,
                                                       nome.groupid AS grpid,
                                                       eq . *
                                            FROM       nome
                                            INNER JOIN
                                                       (
                                                              SELECT equipment.id          AS eqid,
                                                                     equipment.placesid    AS plid,
                                                                     equipment.nomeid      AS nid,
                                                                     equipment.buhname     AS bn,
                                                                     equipment.cost        AS cs,
                                                                     equipment.currentcost AS curc,
                                                                     equipment.invnum,
                                                                     equipment.sernum,
                                                                     equipment.shtrihkod,
                                                                     equipment.mode,
                                                                     equipment.os
                                                              FROM   equipment
                                                              WHERE  equipment.active = 1 $where) AS eq
                                            ON         nome.id = eq.nid) AS res
                      ON         places.id = res.plid) AS res2
ON         group_nome.id = res2.grpid
ORDER BY   $sidx $sord
LIMIT      $start, $limit
TXT;
	try {
		$arr = DB::prepare($sql)->execute()->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['eqid'];
			$responce->rows[$i]['cell'] = array($row['eqid'], $row['plname'],
				$row['namenome'], $row['grname'], $row['invnum'], $row['sernum'],
				$row['shtrihkod'], $row['mode'], $row['os'], $row['bn'], $row['cs'], $row['curc']
			);
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу сформировать список по оргтехнике/помещениям/пользователю! (2)', 0, $ex);
	}
	jsonExit($responce);
}
