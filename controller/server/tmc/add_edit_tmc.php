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

$step = GetDef('step');
$id = GetDef('id');
$name = PostDef('name');
$comment = PostDef('comment');
$groupid = PostDef('groupid');
if ($groupid == '') {
	$err[] = 'Не выбрана группа!';
}
$vendorid = PostDef('vendorid');
if ($vendorid == '') {
	$err[] = 'Не задан производитель!';
}
$namenome = PostDef('namenome');
if ($namenome == '') {
	$err[] = 'Не задано наименование!';
}

if (count($err) == 0) {
	if ($step == 'edit') {
		$sql = "UPDATE nome SET groupid = '$groupid', vendorid = '$vendorid', name = '$namenome' WHERE id = '$id'";
		$sqlcn->ExecuteSQL($sql)
				or die('Не смог обновить номенклатуру!: ' . mysqli_error($sqlcn->idsqlconnection));
	}
	if ($step == 'add') {
		$sql = "INSERT INTO nome (id, groupid, vendorid, name, active) VALUES (NULL, '$groupid', '$vendorid', '$namenome', '1')";
		$sqlcn->ExecuteSQL($sql)
				or die('Не смог добавить номенклатуру!: ' . mysqli_error($sqlcn->idsqlconnection));
	}
}
echo 'ok';
