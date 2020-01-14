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
$id = PostDef('id');
$name = PostDef('name');
$INN = PostDef('INN');
$KPP = PostDef('KPP');
$bayer = PostDef('bayer');
$supplier = PostDef('supplier');
$ERPCode = PostDef('ERPCode');
if (empty($ERPCode)) {
	$ERPCode = '0';
}
$dog = PostDef('dog');
$comment = PostDef('comment');

$filters = GetDef('filters');
$flt = json_decode($filters, true);
$cnt = is_array($flt['rules']) ? count($flt['rules']) : 0;
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
	(($user->mode == 1) || $user->TestRights([1,3,4,5,6])) or die('Недостаточно прав');

	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$sql = "SELECT COUNT(*) AS cnt FROM knt $where";
	try {
		$row = DB::prepare($sql)->execute()->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список контрагентов (1)', 0, $ex);
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
SELECT   id,name,INN,KPP,bayer,supplier,dog,ERPCode,comment,active
FROM     knt
$where
ORDER BY $sidx $sord
LIMIT    $start, $limit
TXT;
	try {
		$arr = DB::prepare($sql)->execute(array())->fetchAll();
		$i = 0;
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['id'];
			$bayer = ($row['bayer'] == '0') ? 'No' : 'Yes';
			$supplier = ($row['supplier'] == '0') ? 'No' : 'Yes';
			$dog = ($row['dog'] == '0') ? 'No' : 'Yes';
			$ic = ($row['active'] == '1') ? 'fa-check-circle-o' : 'fa-ban';
			$responce->rows[$i]['cell'] = array(
				"<i class=\"fa $ic\" aria-hidden=\"true\"></i>",
				$row['id'],
				$row['name'],
				$row['INN'],
				$row['KPP'],
				$bayer,
				$supplier,
				$dog,
				$row['ERPCode'],
				$row['comment']
			);
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список контрагентов', 0, $ex);
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Проверяем может ли пользователь добавлять?
	(($user->mode == 1) || $user->TestRights([1,4])) or die('Недостаточно прав');

	$bayer = ($bayer == 'Yes') ? '1' : '0';
	$supplier = ($supplier == 'Yes') ? '1' : '0';
	$dog = ($dog == 'Yes') ? '1' : '0';
	$sql = <<<TXT
INSERT INTO knt
            (id,name,fullname,INN,KPP,bayer,supplier,dog,ERPCode,comment,active)
VALUES      (NULL, :name, '', :INN, :KPP, :bayer, :supplier, :dog, :ERPCode, :comment, 1)
TXT;
	try {
		DB::prepare($sql)->execute(array(
			':name' => $name,
			':INN' => $INN,
			':KPP' => $KPP,
			':bayer' => $bayer,
			':supplier' => $supplier,
			':dog' => $dog,
			':ERPCode' => $ERPCode,
			':comment' => $comment
		));
	} catch (PDOException $ex) {
		throw new DBException('Не могу добавить контрагента', 0, $ex);
	}
	exit;
}

if ($oper == 'edit') {
	// Проверяем может ли пользователь редактировать?
	(($user->mode == 1) || $user->TestRights([1,5])) or die('Для редактирования не хватает прав!');

	$bayer = ($bayer == 'Yes') ? '1' : '0';
	$supplier = ($supplier == 'Yes') ? '1' : '0';
	$dog = ($dog == 'Yes') ? '1' : '0';
	$sql = <<<TXT
UPDATE knt
SET    name = :name, comment = :comment, INN = :INN, KPP = :KPP, bayer = :bayer, supplier = :supplier, dog = :dog,
       ERPCode = :ERPCode
WHERE  id = :id
TXT;
	try {
		DB::prepare($sql)->execute(array(
			':name' => $name,
			':comment' => $comment,
			':INN' => $INN,
			':KPP' => $KPP,
			':bayer' => $bayer,
			':supplier' => $supplier,
			':dog' => $dog,
			':ERPCode' => $ERPCode,
			':id' => $id
		));
	} catch (PDOException $ex) {
		throw new DBException('Не могу обновить данные по контрагенту', 0, $ex);
	}
	exit;
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	(($user->mode == 1) || $user->TestRights([1,6])) or die('Для удаления не хватает прав!');

	$sql = 'UPDATE knt SET active = NOT active WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не пометить на удаление контрагента', 0, $ex);
	}
	exit;
}
