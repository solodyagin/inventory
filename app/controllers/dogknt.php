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

class dogknt extends controller {

	function index() {
		$data['section'] = 'Инструменты / Контроль договоров';
		$user = user::getInstance();
		if ($user->isAdmin() || $user->testRights([1, 3, 4, 5, 6])) {
			$this->view->renderTemplate('dogknt/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

}
