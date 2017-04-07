<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

class Controller_Account extends Controller {

	function login() {
		global $err;

		$user = User::getInstance();
		$cfg = Config::getInstance();

		$enter_user_login = $_POST['enter_user_login'];
		if ($enter_user_login == '') {
			$err[] = 'Логин не может быть пустым!';
		}
		$enter_user_pass = $_POST['enter_user_pass'];
		if ($enter_user_pass == '') {
			$err[] = 'Пароль не может быть пустым!';
		}
		if (count($err) == 0) { // если буфер ошибок пустой, то ищем пользователя такого
			if ($user->GetByLoginPass($enter_user_login, $enter_user_pass)) { // если нашли, то ставим печеньки
				setcookie('user_randomid_w3', "$user->randomid", strtotime('+30 days'), '/');
			} else { // если не нашли в "обычном" списке, проверяем в AD (если разрешено в настойках)
				if (($cfg->ad == 1) && check_LDAP_user(strtolower($enter_user_login), $enter_user_pass, $cfg->ldap, $cfg->domain1, $cfg->domain2)) {
					if ($user->GetByLogin($enter_user_login)) {// если нашли, то ставим печеньки
						setcookie('user_randomid_w3', "$user->randomid", strtotime('+30 days'), '/');
					} else {
						$err[] = 'Пользователь с таким логином найден в AD, но не найден в базе!';
					}
				} else {
					$err[] = 'Пользователь с таким логином/паролем не найден!';
				}
			}
		}
		Router::redirect('home');
	}

	function logout() {
		$user = User::getInstance();
		$user->id = '';
		$user->randomid = '';
		setcookie('user_randomid_w3', '', 1, '/');
		Router::redirect('home');
	}

}
