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

$page = GetDef('page', 1);
if ($page == 0) {
	$page = 1;
}
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$oper = PostDef('oper');
$id = PostDef('id');

if ($oper == '') {
	$filters = GetDef('filters');

	// получаем наложенные поисковые фильтры
	$flt = json_decode($filters, true);
	$cnt = count($flt['rules']);
	$where = '';
	for ($i = 0; $i < $cnt; $i++) {
		$field = $flt['rules'][$i]['field'];
		$data = $flt['rules'][$i]['data'];
		if ($field == 'groupnomename') {
			$field = 'group_nome.id';
		}
		if ($field == 'idnome') {
			$field = 'equipment.id';
		}
		if ($field == 'nomename') {
			$field = 'nome.name';
		}
		if ($field == 'orgname') {
			$field = 'equipment.orgid';
		}
		if ($data != '-1') {
			if (($field == 'group_nome.id') || ($field == 'equipment.orgid')) {
				$where .= "($field = '$data')";
			} else {
				$where .= "($field LIKE '%$data%')";
			}
		} else {
			$where .= "($field LIKE '%%')";
		}
		if ($i < ($cnt - 1)) {
			$where .= ' AND ';
		}
	}
	if ($where != '') {
		$where = 'WHERE ' . $where;
	}

	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$sql = <<<TXT
SELECT     COUNT(*) AS cnt,
           equipment.repair,
           org.name          AS orgname,
           equipment.id      AS idnome,
           nome.groupid      AS groupid,
           nome.name         AS nomename,
           users_profile.fio AS fio,
           places.name       AS placename,
           group_nome.name   AS grname
FROM       `equipment`
INNER JOIN org
ON         equipment.orgid = org.id
INNER JOIN nome
ON         equipment.nomeid = nome.id
INNER JOIN users_profile
ON         equipment.usersid = users_profile.usersid
INNER JOIN places
ON         equipment.placesid = places.id
INNER JOIN group_nome
ON         group_nome.id = groupid
$where
TXT;
	try {
		$row = DB::prepare($sql)->execute()->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список имущества (1)', 0, $ex);
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
SELECT     equipment.orgid,
           equipment.invnum AS invnum,
           equipment.id     AS id,
           equipment.repair,
           org.name          AS orgname,
           equipment.id      AS idnome,
           nome.groupid      AS groupid,
           nome.name         AS nomename,
           users_profile.fio AS fio,
           places.name       AS placename,
           group_nome.name   AS groupnomename,
           group_nome.id     AS grid
FROM       `equipment`
INNER JOIN org
ON         equipment.orgid = org.id
INNER JOIN nome
ON         equipment.nomeid = nome.id
INNER JOIN users_profile
ON         equipment.usersid = users_profile.usersid
INNER JOIN places
ON         equipment.placesid = places.id
INNER JOIN group_nome
ON         group_nome.id = groupid
$where
ORDER BY   $sidx $sord
LIMIT      $start, $limit
TXT;
	try {
		$arr = DB::prepare($sql)->execute()->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['id'];
			switch ($row['repair']) {
				case 0: $st = 'Работает';
					break;
				case 1: $st = 'В сервисе';
					break;
				case 2: $st = 'Есть заявка';
					break;
				case 3: $st = 'Списать';
					break;
			}
			$eqid = $row['id'];
			$sql = <<<TXT
SELECT COUNT(id) AS cntmonth
FROM   repair
WHERE  dt > DATE_ADD(NOW(), INTERVAL -31 DAY)
       AND eqid = :eqid
TXT;
			$row2 = DB::prepare($sql)->execute(array(':eqid' => $eqid))->fetch();
			if ($row2) {
				$cnm = $row2['cntmonth'];
			}

			$sql = <<<TXT
SELECT COUNT(id) AS cntyear
FROM   repair
WHERE  dt > DATE_ADD(NOW(), INTERVAL -365 DAY)
       AND eqid = :eqid
TXT;
			$row2 = DB::prepare($sql)->execute(array(':eqid' => $eqid))->fetch();
			if ($row2) {
				$cny = $row2['cntyear'];
			}

			$responce->rows[$i]['cell'] = array($st, $row['orgname'], $row['placename'],
				$row['groupnomename'], $row['id'], $row['invnum'], $row['nomename'],
				$row['fio'], $cnm, $cny
			);
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список имущества (2)', 0, $ex);
	}
	jsonExit($responce);
}
