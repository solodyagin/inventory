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

namespace app\controllers;

use core\controller;
use core\user;

class ping extends controller {

	function index() {
		$user = user::getInstance();
		$data['section'] = 'Инструменты / Проверка доступности';
		if ($user->isAdmin() || $user->testRights([1])) {
			$this->view->renderTemplate('ping/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

}
