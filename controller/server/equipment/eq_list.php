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

	$sql = <<<TXT
SELECT COUNT(*) AS cnt,name AS grname,res2.*
FROM   group_nome
       INNER JOIN (SELECT places.name AS plname,res.*
                   FROM   places
                          INNER JOIN(SELECT name AS namenome,nome.groupid AS grpid,eq.*
                                     FROM   nome
                                            INNER JOIN (SELECT equipment.id AS eqid,equipment.placesid AS plid,
                                                               equipment.nomeid AS nid,
                                                               equipment.buhname AS bn,
                                                                                       equipment.cost AS cs,
                                                                                       equipment.currentcost AS curc,
                                                               equipment.invnum,
                                                               equipment.sernum,
                                                                            equipment.shtrihkod,
                                                                                       equipment.mode,equipment.os
                                                        FROM   equipment
                                                               INNER JOIN (SELECT placesid
                                                                           FROM   places_users
                                                                           WHERE  userid = '$curuserid') AS pl
                                                                       ON pl.placesid = equipment.placesid
                                                        WHERE  equipment.active = 1) AS eq
                                                    ON nome.id = eq.nid) AS res
                                  ON places.id = res.plid) AS res2
               ON group_nome.id = res2.grpid
TXT;
	try {
		$row = DB::prepare($sql)->execute()->fetch();
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
                                                       eq.*
                                            FROM       nome
                                            INNER JOIN
                                                       (
                                                                  SELECT     equipment.id          AS eqid,
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
                                                                  FROM       equipment
                                                                  INNER JOIN
                                                                             (
                                                                                    SELECT placesid
                                                                                    FROM   places_users
                                                                                    WHERE  userid = '$curuserid' ) AS pl
                                                                  ON         pl.placesid = equipment.placesid
                                                                  WHERE      equipment.active = 1 ) AS eq
                                            ON         nome.id = eq.nid ) AS res
                      ON         places.id = res.plid
                      ORDER BY   $sidx $sord
                      LIMIT      $start, $limit ) AS res2
ON         group_nome.id = res2.grpid
TXT;
	try {
		$arr = DB::prepare($sql)->execute()->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['eqid'];
			$responce->rows[$i]['cell'] = array($row['eqid'], $row['plname'],
				$row['namenome'], $row['grname'], $row['invnum'], $row['sernum'],
				$row['shtrihkod'], $row['mode']);
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать сформировать список по оргтехнике/помещениям/пользователю (2)', 0, $ex);
	}
	jsonExit($responce);
}
