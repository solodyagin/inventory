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

class Controller_Dogknt extends Controller {

	function index() {
		$user = User::getInstance();
		$cfg = Config::getInstance();
		$data['section'] = 'Инструменты / Контроль договоров';
		if ($user->isAdmin() || $user->TestRights([1,3,4,5,6])) {
			$this->view->generate('dogknt/index', $cfg->theme, $data);
		} else {
			$this->view->generate('restricted', $cfg->theme, $data);
		}
	}

}
