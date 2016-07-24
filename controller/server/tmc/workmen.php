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
$id = PostDef('id');

/////////////////////////////
// вычисляем фильтр
/////////////////////////////
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
		if (($field == 'group_nome.id') or ( $field == 'equipment.orgid')) {
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
/////////////////////////////

if ($oper == '') {
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
	$result = $sqlcn->ExecuteSQL($sql);
	$row = mysqli_fetch_array($result);
	$count = $row['cnt'];
	$total_pages = ($count > 0) ? ceil($count / $limit) : 0;
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
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
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать список производителей! ' . mysqli_error($sqlcn->idsqlconnection));
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
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
       AND eqid = '$eqid'
TXT;
		$result2 = $sqlcn->ExecuteSQL($sql);
		while ($row2 = mysqli_fetch_array($result2)) {
			$cnm = $row2['cntmonth'];
		}
		$sql = <<<TXT
SELECT COUNT(id) AS cntyear
FROM   repair
WHERE  dt > DATE_ADD(NOW(), INTERVAL -365 DAY)
       AND eqid = '$eqid'
TXT;
		$result2 = $sqlcn->ExecuteSQL($sql);
		while ($row2 = mysqli_fetch_array($result2)) {
			$cny = $row2['cntyear'];
		}
		$responce->rows[$i]['cell'] = array($st, $row['orgname'], $row['placename'], $row['groupnomename'], $row['id'], $row['invnum'], $row['nomename'], $row['fio'], $cnm, $cny);
		$i++;
	}
	jsonExit($responce);
}
