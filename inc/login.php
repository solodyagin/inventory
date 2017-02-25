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

$user = new User();

// Если есть печеньки, то получаем сессионный идентификатор
$user->randomid = (isset($_COOKIE['user_randomid_w3'])) ? $_COOKIE['user_randomid_w3'] : '';

// если есть кукисы, то заполняем данные по пользователю ГЛОБАЛЬНО в переменную $user
// если кукисов нет, или они не верные,то $user->randomid делаем пустым
if ($user->randomid != '') {
	if ($user->GetByRandomId($user->randomid)) {
		$user->UpdateLastdt($user->id); // обновляем дату последнего входа пользователя
		SetCookie('user_randomid_w3', "$user->randomid", strtotime('+30 days'), '/'); // ну и обновляем заодно время жизни печеньки
	} else {
		$user->randomid = '';
		SetCookie('user_randomid_w3', '', 1, '/'); // удаляем куки
	}
}

// обрабатываем попытку войти/зарегистрироваться
if (isset($_GET['login_step'])) {
	if ($_GET['login_step'] == 'logout') { // если выход то стираем кукисы и ГО на главную страницу
		$user->id = '';
		$user->randomid = '';
		SetCookie('user_randomid_w3', '', 1, '/');
	}
	if ($_GET['login_step'] == 'enter') { // если вход то пытаемся зайти
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
				SetCookie('user_randomid_w3', "$user->randomid", strtotime('+30 days'), '/');
			} else { // если не нашли в "обычном" списке, проверяем в AD (если разрешено в настойках)
				if (($cfg->ad == 1) && check_LDAP_user(strtolower($enter_user_login), $enter_user_pass, $cfg->ldap, $cfg->domain1, $cfg->domain2)) {
					if ($user->GetByLogin($enter_user_login)) {// если нашли, то ставим печеньки
						SetCookie('user_randomid_w3', "$user->randomid", strtotime('+30 days'), '/');
					} else {
						$err[] = 'Пользователь с таким логином найден в AD, но не найден в базе!';
					}
				} else {
					$err[] = 'Пользователь с таким логином/паролем не найден!';
				}
			}
		}
	}
}
