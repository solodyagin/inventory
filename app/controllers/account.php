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
use core\config;
use core\router;
use core\user;
use core\utils;

class account extends controller {

	function login() {
		global $err;
		$login = filter_input(INPUT_POST, 'login');
		if ($login == '') {
			$err[] = 'Логин не может быть пустым!';
		}
		$password = filter_input(INPUT_POST, 'password');
		if ($password == '') {
			$err[] = 'Пароль не может быть пустым!';
		}
		if (count($err) == 0) { // если буфер ошибок пустой, то ищем пользователя такого
			$user = user::getInstance();
			$cfg = config::getInstance();
			if (!$user->loginByDB($login, $password)) { // если не нашли в "обычном" списке, проверяем в AD (если разрешено в настойках)
				if (($cfg->ad == 1) && utils::checkLDAPuser(strtolower($login), $password, $cfg->ldap, $cfg->domain1, $cfg->domain2)) {
					if ($user->getByLogin($login)) {// если нашли, то ставим печеньки
						setcookie("inventory_{$cfg->inventory_id}", $user->randomid, strtotime('+30 days'), '/');
					} else {
						$err[] = 'Пользователь с таким логином найден в AD, но не найден в базе!';
					}
				} else {
					$err[] = 'Пользователь с таким логином/паролем не найден!';
				}
			}
		}
		router::redirect('main');
	}

	function logout() {
		$user = user::getInstance();
		$user->logout();
		router::redirect('main');
	}

}
