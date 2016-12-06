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

// Проверка: может ли пользователь добавлять?
(($user->mode == 1) || $user->TestRoles('1,4')) or die('У вас не хватает прав на добавление!');

$foldername = GetDef('foldername');

$sql = 'INSERT INTO cloud_dirs (parent, name) VALUES (0, :foldername)';
try {
	DB::prepare($sql)->execute(array(':foldername' => $foldername));
} catch (PDOException $ex) {
	throw new DBException('Не могу добавить папку', 0, $ex);
}
