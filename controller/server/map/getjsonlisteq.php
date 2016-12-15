<?php

/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$orgid = GetDef('orgid');
$selpom = GetDef('selpom');
$spom = ($selpom != 'null') ? " AND equipment.placesid = $selpom" : '';

$sql = <<<TXT
SELECT     equipment.os       AS os,
           equipment.mode     AS eqmode,
           equipment.datepost AS dtpost,
           equipment.active   AS active,
           equipment.photo    AS photo,
           equipment.mapmoved AS mapmoved,
           getvendorandgroup.grnomeid,
           equipment.mapx               AS mapx,
           equipment.mapy               AS mapy,
           equipment.id                 AS eqid,
           equipment.orgid              AS eqorgid,
           org.name                     AS orgname,
           getvendorandgroup.vendorname AS vname,
           getvendorandgroup.groupname  AS grnome,
           places.name                  AS placesname,
           users.login                  AS userslogin,
           getvendorandgroup.nomename   AS nomename,
           buhname,
           sernum,
           invnum,
           shtrihkod,
           datepost,
           cost,
           currentcost,
           os,
           equipment.mode    AS eqmode,
           equipment.comment AS eqcomment,
           equipment.active  AS eqactive,
           equipment.repair  AS eqrepair
FROM       equipment
INNER JOIN
           (
                      SELECT     nome.groupid    AS grnomeid,
                                 nome.id         AS nomeid,
                                 vendor.name     AS vendorname,
                                 group_nome.name AS groupname,
                                 nome.name       AS nomename
                      FROM       nome
                      INNER JOIN group_nome
                      ON         nome.groupid = group_nome.id
                      INNER JOIN vendor
                      ON         nome.vendorid = vendor.id ) AS getvendorandgroup
ON         getvendorandgroup.nomeid = equipment.nomeid
INNER JOIN org
ON         org.id = equipment.orgid
INNER JOIN places
ON         places.id = equipment.placesid
INNER JOIN users
ON         users.id = equipment.usersid
WHERE      equipment.orgid = :orgid
AND        mapyet = 1 $spom
TXT;

$responce = new stdClass();

try {
	$arr = DB::prepare($sql)->execute(array(':orgid' => $orgid))->fetchAll();
	$i = 0;
	foreach ($arr as $row) {
		$responce->rows[$i]['poz'] = $i;
		$responce->rows[$i]['cell'] = array(
			$row['active'], $row['eqid'], $row['placesname'], $row['nomename'], $row['mapx'], $row['mapy'],
			$row['grnome'], $row['vname'], $row['buhname'], $row['sernum'], $row['invnum'],
			$row['shtrihkod'], $row['orgname'], $row['userslogin'], $row['dtpost'], $row['cost'],
			$row['currentcost'], $row['os'], $row['eqmode'], $row['eqcomment'], $row['eqrepair'], $row['mapmoved'], $row['photo']);
		$i++;
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать сформировать список по оргтехнике/помещениям/пользователю', 0, $ex);
}

jsonExit($responce);
