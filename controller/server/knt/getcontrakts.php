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
$idknt = GetDef('idknt');
$name = PostDef('name');
$num = PostDef('num');
$datestart = PostDef('datestart');
$dateend = PostDef('dateend');
$work = PostDef('work');
$comment = PostDef('comment');

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	$user->TestRoles('1,3,4,5,6') or die('Недостаточно прав');
	$where = "WHERE kntid = '$idknt'";
	$result = $sqlcn->ExecuteSQL("SELECT COUNT(*) AS cnt FROM contract $where");
	$row = mysqli_fetch_array($result);
	$count = $row['cnt'];
	$total_pages = ($count > 0) ? ceil($count / $limit) : 0;
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
	$sql = "SELECT * FROM contract $where ORDER BY $sidx $sord LIMIT $start, $limit";
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать список договоров!' . mysqli_error($sqlcn->idsqlconnection));
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
		$responce->rows[$i]['id'] = $row['id'];
		$work = ($row['work'] == 0) ? 'No' : 'Yes';
		$dateend = $row['dateend'] . ' 00:00:00';
		$datestart = $row['datestart'] . ' 00:00:00';
		if ($row['active'] == '1') {
			$responce->rows[$i]['cell'] = array('<i class="fa fa-check-circle-o" aria-hidden="true"></i>', $row['id'], $row['num'], $row['name'], MySQLDateTimeToDateTime($datestart), MySQLDateTimeToDateTime($dateend), $work, $row['comment']);
		} else {
			$responce->rows[$i]['cell'] = array('<i class="fa fa-ban" aria-hidden="true"></i>', $row['id'], $row['num'], $row['name'], MySQLDateTimeToDateTime($datestart), MySQLDateTimeToDateTime($dateend), $work, $row['comment']);
		}
		$i++;
	}
	jsonExit($responce);
}

if ($oper == 'add') {
	// Проверяем может ли пользователь добавлять?
	$user->TestRoles('1,4') or die('Недостаточно прав');
	$work = ($work == 'Yes') ? '1' : '0';
	$datestart = DateToMySQLDateTime2($datestart);
	$dateend = DateToMySQLDateTime2($dateend);
	$sql = <<<TXT
INSERT INTO contract
            (id,kntid,num,name,comment,datestart,dateend,work,active)
VALUES      (NULL,'$idknt','$num','$name','$comment','$datestart','$dateend','$work',1)
TXT;
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу добавить данные по договору!' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'edit') {
	// Проверяем может ли пользователь редактировать?
	$user->TestRoles('1,5') or die('Для редактирования не хватает прав!');
	$work = ($work == 'Yes') ? '1' : '0';
	$datestart = DateToMySQLDateTime2($datestart);
	$dateend = DateToMySQLDateTime2($dateend);
	$sql = <<<TXT
UPDATE contract
SET    num = '$num',name = '$name',comment = '$comment',datestart = '$datestart',dateend = '$dateend',work = '$work'
WHERE  id = '$id'
TXT;
	$sqlcn->ExecuteSQL($sql)
			or die('Не могу обновить данные по договору!' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	$user->TestRoles('1,6') or die('Для удаления не хватает прав!');
	$sql = "UPDATE contract SET active = NOT active WHERE id = '$id'";
	$sqlcn->ExecuteSQL($sql)
			or die('Не смог пометить на удаление договор!' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}
