<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

/* Объявляем глобальные переменные */
define('WUO', true);
define('WUO_ROOT', dirname(__FILE__));
define('WUO_VERSION', '2017-04');

$err = array(); // Массив с сообщениями об ошибках для показа пользователю при генерации страницы
$ok = array(); // Массив с информационными сообщениями для показа пользователю при генерации страницы

/* Некоторые установки */
date_default_timezone_set('Europe/Moscow'); // Временная зона по умолчанию

/* Загружаем первоначальные настройки. Если не получилось - запускаем инсталлятор */
$rez = @include_once(WUO_ROOT . '/config.php');
if ($rez == false) {
	header('Location: install/index.php');
	die();
}

$time_start = microtime(true); // Засекаем время начала выполнения скрипта

header('Content-Type: text/html; charset=utf-8');

/* Загружаем движок */
include_once(WUO_ROOT . '/bootstrap.php');

/* Загружаем все что нужно для работы движка */
include_once(WUO_ROOT . '/inc/config.php'); // Подгружаем настройки из БД, получаем заполненый класс $cfg
include_once(WUO_ROOT . '/inc/functions.php'); // Загружаем функции
include_once(WUO_ROOT . '/inc/login.php'); // Создаём пользователя $user

/* Если указан маршрут, то подключаем указанный в маршруте скрипт и выходим */
$uri = $_SERVER['REQUEST_URI'];
if (strpos($uri, $rewrite_base) === 0) {
	$uri = substr($uri, strlen($rewrite_base));
}
if (strpos($uri, 'route') === 0) {
	// Удаляем лишнее
	$uri = substr($uri, 5);

	// Получаем путь до скрипта ($route) и переданные ему параметры ($PARAMS)
	list($route, $p) = array_pad(explode('?', $uri, 2), 2, null);
	if ($p) {
		parse_str($p, $PARAMS);
	}

	// Разрешаем подключать php-скрипты только из каталогов /controller и /inc
	if ((!preg_match('#^(/controller)|(/inc)#', $route)) || (strpos($route, '..') !== false)) {
		die("Запрещён доступ к '$route'");
	}

	// Подключаем запрашиваемый скрипт
	if (is_file(WUO_ROOT . $route)) {
		// Разрешаем доступ только выполнившим вход пользователям
		if ($user->id == '') {
			die('Доступ ограничен');
		}
		include_once(WUO_ROOT . $route);
	} else {
		die("На сервере отсутствует указанный путь '$route'");
	}
	exit;
}

/* Загружаем сторонние классы */
include_once(WUO_ROOT . '/libs/class.phpmailer.php'); // Класс управления почтой

/* Запускаем сторонние скрипты */
include_once(WUO_ROOT . '/inc/autorun.php');

/* Инициализируем заполнение меню */
$gmenu = new Menu();
$gmenu->GetFromFiles(WUO_ROOT . '/inc/menu');

// Если активен режим отладки, то показываем все ошибки и предупреждения
if ($debug) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}

/* Запускаем маршрутизатор */
Router::start();
