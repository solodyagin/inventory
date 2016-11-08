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
$name = PostDef('name');
$INN = PostDef('INN');
$KPP = PostDef('KPP');
$bayer = PostDef('bayer');
$supplier = PostDef('supplier');
$ERPCode = PostDef('ERPCode');
$dog = PostDef('dog');
$comment = PostDef('comment');

$filters = GetDef('filters');
$flt = json_decode($filters, true);
$cnt = count($flt['rules']);
$where = '';
for ($i = 0; $i < $cnt; $i++) {
	$field = $flt['rules'][$i]['field'];
	$data = $flt['rules'][$i]['data'];
	if ($data != '-1') {
		$where = $where . "($field LIKE '%$data%')";
	} else {
		$where = $where . "($field LIKE '%%')";
	}
	if ($i < ($cnt - 1)) {
		$where = $where . ' AND ';
	}
}
if ($where != '') {
	$where = 'WHERE ' . $where;
}

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	(($user->mode == 1) || $user->TestRoles('1,3,4,5,6')) or die('Недостаточно прав');
	$result = $sqlcn->ExecuteSQL("SELECT COUNT(*) AS cnt FROM knt $where");
	$row = mysqli_fetch_array($result);
	$count = $row['cnt'];
	$total_pages = ($count > 0) ? ceil($count / $limit) : 0;
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
	$sql = <<<TXT
SELECT   id,
         name,
         INN,
         KPP,
         bayer,
         supplier,
         dog,
         ERPCode,
         comment,
         active
FROM     knt
$where
ORDER BY $sidx $sord
LIMIT    $start, $limit
TXT;
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать список контрагентов!' . mysqli_error($sqlcn->idsqlconnection));
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
		$responce->rows[$i]['id'] = $row['id'];
		$bayer = ($row['bayer'] == '0') ? 'No' : 'Yes';
		$supplier = ($row['supplier'] == '0') ? 'No' : 'Yes';
		$dog = ($row['dog'] == '0') ? 'No' : 'Yes';
		if ($row['active'] == '1') {
			$responce->rows[$i]['cell'] = array('<i class="fa fa-check-circle-o" aria-hidden="true"></i>', $row['id'], $row['name'], $row['INN'], $row['KPP'], $bayer, $supplier, $dog, $row['ERPCode'], $row['comment']);
		} else {
			$responce->rows[$i]['cell'] = array('<i class="fa fa-ban" aria-hidden="true"></i>', $row['id'], $row['name'], $row['INN'], $row['KPP'], $bayer, $supplier, $dog, $row['ERPCode'], $row['comment']);
		}
		$i++;
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Проверяем может ли пользователь добавлять?
	(($user->mode == 1) || $user->TestRoles('1,4')) or die('Недостаточно прав');
	$bayer = ($bayer == 'Yes') ? '1' : '0';
	$supplier = ($supplier == 'Yes') ? '1' : '0';
	$dog = ($dog == 'Yes') ? '1' : '0';
	$sql = <<<TXT
INSERT INTO knt
            (id,name,INN,KPP,bayer,supplier,dog,ERPCode,comment,active)
VALUES      (NULL,'$name','$INN','$KPP','$bayer','$supplier','$dog','$ERPCode','$comment',1)
TXT;
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу добавить контрагента! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'edit') {
	// Проверяем может ли пользователь редактировать?
	(($user->mode == 1) || $user->TestRoles('1,5')) or die('Для редактирования не хватает прав!');
	$bayer = ($bayer == 'Yes') ? '1' : '0';
	$supplier = ($supplier == 'Yes') ? '1' : '0';
	$dog = ($dog == 'Yes') ? '1' : '0';
	$sql = <<<TXT
UPDATE knt
SET    name = '$name',comment = '$comment',INN = '$INN',KPP = '$KPP',bayer = '$bayer',supplier = '$supplier',dog =
       '$dog',
       ERPCode = '$ERPCode'
WHERE  id = '$id'
TXT;
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу обновить данные по контрагенту! ' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	(($user->mode == 1) || $user->TestRoles('1,6')) or die('Для удаления не хватает прав!');
	$sql = "UPDATE knt SET active = NOT active WHERE id = '$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу обновить данные по контрагенту!' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}
