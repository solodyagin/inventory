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
$sorgider = GetDef('sorgider', $cfg->defaultorgid);
$id = PostDef('id');
$ip = PostDef('ip');
$name = PostDef('name');
$comment = PostDef('comment');
$buhname = PostDef('buhname');
$sernum = PostDef('sernum');
$invnum = PostDef('invnum');
$shtrihkod = PostDef('shtrihkod');
$cost = PostDef('cost');
$currentcost = PostDef('currentcost');
$os = PostDef('os');
$tmcgo = PostDef('tmcgo');
$mode = PostDef('mode');
//$mapyet = PostDef('mapyet');
$mapyet = PostDef('eqmapyet');
$orgid = $cfg->defaultorgid;

$user = User::getInstance();

if ($oper == '') {
	// Проверка: может ли пользователь просматривать?
	($user->isAdmin() || $user->TestRoles('1,3,4,5,6')) or die('Недостаточно прав');

	// получаем наложенные поисковые фильтры
	$filters = GetDef('filters');
	$flt = json_decode($filters, true);
	$cnt = count($flt['rules']);
	$where = '';
	for ($i = 0; $i < $cnt; $i++) {
		$field = $flt['rules'][$i]['field'];
		if ($field == 'org.name') {
			$field = 'org.id';
		}
		$data = $flt['rules'][$i]['data'];
		if ($data != '-1') {
			if (($field == 'placesid') || ($field == 'getvendorandgroup.grnomeid')) {
				$where = $where . "($field = '$data')";
			} else {
				$where = $where . "($field LIKE '%$data%')";
			}
		} else {
			$where = $where . "($field LIKE '%%')";
		}
		if ($i < ($cnt - 1)) {
			$where = $where . ' AND ';
		}
	}
	if ($where == '') {
		$where = "WHERE equipment.orgid='$sorgider'";
	} else {
		$where = "WHERE $where AND equipment.orgid='$sorgider'";
	}

	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$sql = 'SELECT COUNT(*) AS cnt FROM equipment';
	try {
		$row = DB::prepare($sql)->execute()->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new DBException('Не получилось выбрать список оргтехники!', 0, $ex);
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
SELECT     equipment.dtendgar,
           tmcgo,
           knt.name AS kntname,
           getvendorandgroup.grnomeid,
           equipment.id                 AS eqid,
           equipment.ip                 AS ip,
           equipment.orgid              AS eqorgid,
           org.name                     AS orgname,
           getvendorandgroup.vendorname AS vname,
           getvendorandgroup.groupname  AS grnome,
           places.name                  AS placesname,
           users_profile.fio            AS fio,
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
           equipment.mapyet  AS eqmapyet,
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
INNER JOIN users_profile
ON         users_profile.usersid = equipment.usersid
LEFT JOIN  knt
ON         knt.id = equipment.kntid $where
ORDER BY   $sidx $sord
LIMIT      $start, $limit
TXT;
	try {
		$arr = DB::prepare($sql)->execute()->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['eqid'];
			if ($row['eqactive'] == '1') {
				$active = '<i class="fa fa-check-circle" aria-hidden="true"></i>';
			} else {
				$active = '<i class="fa fa-ban" aria-hidden="true"></i>';
			}
			if ($row['eqrepair'] == '1') {
				$active = $active . '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>';
			}
			$os = ($row['os'] == '0') ? 'No' : 'Yes';
			$eqmode = ($row['eqmode'] == '0') ? 'No' : 'Yes';
			$eqmapyet = ($row['eqmapyet'] == '0') ? 'No' : 'Yes';
			$dtpost = MySQLDateTimeToDateTime($row['datepost']);
			$dtendgar = MySQLDateToDate($row['dtendgar']);
			$tmcgo = ($row['tmcgo'] == '0') ? 'No' : 'Yes';
			$responce->rows[$i]['cell'] = array(
				$active, $row['eqid'], $row['ip'], $row['placesname'],
				$row['nomename'], $row['grnome'], $tmcgo, $row['vname'],
				$row['buhname'], $row['sernum'], $row['invnum'], $row['shtrihkod'],
				$row['orgname'], $row['fio'], $dtpost, $row['cost'],
				$row['currentcost'], $os, $eqmode, $row['eqmapyet'],
				$row['eqcomment'], $row['eqrepair'], $dtendgar, $row['kntname']
			);
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не получилось выбрать список оргтехники!', 0, $ex);
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Проверка: может ли пользователь добавлять?
	($user->isAdmin() || $user->TestRoles('1,4')) or die('Недостаточно прав');

	$sql = 'INSERT INTO places (id, orgid, name, comment, active) VALUES (null, :orgid, :name, :comment, 1)';
	try {
		DB::prepare($sql)->execute(array(
			':orgid' => $orgid,
			':name' => $name,
			':comment' => $comment
		));
	} catch (PDOException $ex) {
		throw new DBException('Не смог добавить оргтехнику!', 0, $ex);
	}
	exit;
}

if ($oper == 'edit') {
	// Проверка: может ли пользователь редактировать?
	($user->isAdmin() || $user->TestRoles('1,5')) or die('Недостаточно прав');

	$os = ($os == 'Yes') ? 1 : 0;
	$tmcgo = ($tmcgo == 'Yes') ? 1 : 0;
	$mode = ($mode == 'Yes') ? 1 : 0;
	$mapyet = ($mapyet == 'Yes') ? 1 : 0;

	$sql = <<<TXT
UPDATE equipment
SET    buhname = :buhname, sernum = :sernum, invnum = :invnum, shtrihkod = :shtrihkod, cost = :cost,
       currentcost = :currentcost, os = :os, mode = :mode, mapyet = :mapyet, comment = :comment, tmcgo = :tmcgo
WHERE  id = :id
TXT;
	try {
		DB::prepare($sql)->execute(array(
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
		));
	} catch (PDOException $ex) {
		throw new DBException('Не смог обновить оргтехнику!', 0, $ex);
	}
	exit;
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	($user->isAdmin() || $user->TestRoles('1,6')) or die('Недостаточно прав');

	$sql = 'UPDATE equipment SET active = NOT active WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не смог пометить на удаление оргтехнику!', 0, $ex);
	}
	exit;
}
