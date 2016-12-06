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

// Выполняем только при наличии у пользователя соответствующей роли
// http://грибовы.рф/wiki/doku.php/основы:доступ:роли
(($user->mode == 1) || $user->TestRoles('1,3,4,5,6')) or die('Недостаточно прав');

$foldername = GetDef('foldername');

function GetTree($key) {
	$sql = 'SELECT * FROM cloud_dirs WHERE parent = :key';
	try {
		$arr = DB::prepare($sql)->execute(array(':key' => $key))->fetchAll();
		$pz = 0;
		foreach ($arr as $row) {
			$name = $row['name'];
			$key = $row['id'];
			echo '{"title":"' . $name . '","isFolder":true,"key":"' . $key . '","children":[';
			GetTree($key);
			echo ']}';
			$pz++;
			if ($pz < count($arr)) {
				echo ',';
			}
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу прочитать папку', 0, $ex);
	}
}

echo '[';

// читаю корневые папки
$sql = 'SELECT * FROM cloud_dirs WHERE parent = 0';
try {
	$arr = DB::prepare($sql)->execute()->fetchAll();
	$pz = 0;
	foreach ($arr as $row) {
		$name = $row['name'];
		$key = $row['id'];
		echo '{"title":"' . $name . '","isFolder":true,"key":"' . $key . '","children":[';
		GetTree($key);
		echo ']}';
		$pz++;
		if ($pz < count($arr)) {
			echo ',';
		}
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу прочитать папку', 0, $ex);
}

echo ']';
