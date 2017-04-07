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

$user = User::getInstance();

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
