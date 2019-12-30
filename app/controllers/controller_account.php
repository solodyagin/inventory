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

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

class Controller_Account extends Controller {

	function login() {
		global $err;

		$user = User::getInstance();
		$cfg = Config::getInstance();

		$login = filter_input(INPUT_POST, 'login');
		if ($login == '') {
			$err[] = 'Логин не может быть пустым!';
		}
		$password = filter_input(INPUT_POST, 'password');
		if ($password == '') {
			$err[] = 'Пароль не может быть пустым!';
		}
		if (count($err) == 0) { # если буфер ошибок пустой, то ищем пользователя такого
			if (!$user->loginByDB($login, $password)) { # если не нашли в "обычном" списке, проверяем в AD (если разрешено в настойках)
				if (($cfg->ad == 1) && check_LDAP_user(strtolower($login), $password, $cfg->ldap, $cfg->domain1, $cfg->domain2)) {
					if ($user->getByLogin($login)) {// если нашли, то ставим печеньки
						setcookie('user_randomid_w3', "$user->randomid", strtotime('+30 days'), '/');
					} else {
						$err[] = 'Пользователь с таким логином найден в AD, но не найден в базе!';
					}
				} else {
					$err[] = 'Пользователь с таким логином/паролем не найден!';
				}
			}
		}
		Router::redirect('main');
	}

	function logout() {
		$user = User::getInstance();
		$user->logout();
		Router::redirect('main');
	}

}
