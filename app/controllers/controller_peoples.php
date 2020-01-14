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

/* Запрещаем прямой вызов скрипта. */
defined('SITE_EXEC') or die('Доступ запрещён');

class Controller_Peoples extends Controller {

	function index() {
		$user = User::getInstance();
		$cfg = Config::getInstance();
		$data['section'] = 'Справочники / Сотрудники';
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->generate('peoples/index', $cfg->theme, $data);
		} else {
			$this->view->generate('restricted', $cfg->theme, $data);
		}
	}

	/** Форма добавления сотрудника */
	function add() {
		$user = User::getInstance();
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->generate('peoples/add', '');
		} else {
			$data['section'] = 'Справочники / Сотрудники';
			$this->view->generate('restricted', '', $data);
		}
	}

	/** Форма редактирования сотрудника */
	function edit() {
		$user = User::getInstance();
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->generate('peoples/edit', '');
		} else {
			$data['section'] = 'Справочники / Сотрудники';
			$this->view->generate('restricted', '', $data);
		}
	}

}
