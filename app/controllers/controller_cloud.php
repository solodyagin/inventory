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

class Controller_Cloud extends Controller {

	function index_get() {
		$cfg = Config::getInstance();
		$this->view->generate('view_cloud', $cfg->theme);
	}

	function addfolder_get() {
		$user = User::getInstance();

		// Проверка: может ли пользователь добавлять?
		($user->isAdmin() || $user->TestRoles('1,4')) or die('У вас не хватает прав на добавление!');

		$foldername = GetDef('foldername');

		$sql = 'INSERT INTO cloud_dirs (parent, name) VALUES (0, :foldername)';
		try {
			DB::prepare($sql)->execute(array(':foldername' => $foldername));
		} catch (PDOException $ex) {
			throw new DBException('Не могу добавить папку', 0, $ex);
		}
	}

	function delfolder_get() {
		$user = User::getInstance();

		// Проверка: может ли пользователь удалять?
		($user->isAdmin() || $user->TestRoles('1,6')) or die('У вас не хватает прав на удаление!');

		$folderkey = GetDef('folderkey');

		$sql = 'DELETE FROM cloud_dirs WHERE id = :folderkey';
		try {
			DB::prepare($sql)->execute(array(':folderkey' => $folderkey));
		} catch (PDOException $ex) {
			throw new DBException('Не могу удалить папку', 0, $ex);
		}
	}

}
