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

//namespace App\Controllers;
//use Core\Controller;
//use Core\Config;
//use Core\Router;
//use Core\User;
//use Core\DB;
//use \PDOException;
//use Core\DBException;

class Controller_Dogknt extends Controller {

	function index() {
		$user = User::getInstance();
		$data['section'] = 'Инструменты / Контроль договоров';
		if ($user->isAdmin() || $user->TestRights([1, 3, 4, 5, 6])) {
			$this->view->renderTemplate('dogknt/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

}
