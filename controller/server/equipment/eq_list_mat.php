<?php

/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$page = GetDef('page', '1');
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$oper = PostDef('oper');
$curuserid = GetDef('curuserid');
$id = PostDef('id');

if ($oper == '') {
	$sql = <<<TXT
SELECT COUNT(*) AS cnt,name AS grname,res2.*
FROM   group_nome
       INNER JOIN (SELECT places.name AS plname,res.*
                   FROM   places
                          INNER JOIN (SELECT name AS namenome,nome.groupid AS grpid,eq.*
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
                                                         WHERE  equipment.active = 1
                                                                AND equipment.usersid = '$curuserid') AS eq
                                                     ON nome.id = eq.nid) AS res
                                  ON places.id = res.plid) AS res2
               ON group_nome.id = res2.grpid
TXT;
	$result = $sqlcn->ExecuteSQL($sql);
	$row = mysqli_fetch_array($result);
	$count = $row['cnt'];
	$total_pages = ($count > 0) ? ceil($count / $limit) : 0;
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
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
                                                              WHERE  equipment.active = 1
                                                              AND    equipment.usersid = '$curuserid' ) AS eq
                                            ON         nome.id = eq.nid ) AS res
                      ON         places.id = res.plid
                      ORDER BY   $sidx $sord
                      LIMIT      $start, $limit ) AS res2
ON         group_nome.id = res2.grpid
TXT;
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать сформировать список по оргтехнике/помещениям/пользователю! ' . mysqli_error($sqlcn->idsqlconnection));
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
		$responce->rows[$i]['id'] = $row['eqid'];
		$responce->rows[$i]['cell'] = array($row['eqid'], $row['plname'],
			$row['namenome'], $row['grname'], $row['invnum'], $row['sernum'],
			$row['shtrihkod'], $row['mode'], $row['os'], $row['cs'],
			$row['curc'], $row['bn']);
		$i++;
	}
	jsonExit($responce);
}
