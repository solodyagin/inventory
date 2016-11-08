<?php

/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

/* Объявляем глобальные переменные */
define('WUO_ROOT', dirname(__FILE__));
$err = array(); // Массив с сообщениями об ошибках для показа пользователю при генерации страницы
$ok = array(); // Массив с информационными сообщениями для показа пользователю при генерации страницы

/* Некоторые установки */
date_default_timezone_set('Europe/Moscow'); // Временная зона по умолчанию

/* Загружаем первоначальные настройки. Если не получилось - запускаем инсталлятор */
$rez = @include_once(WUO_ROOT . '/config.php');
if ($rez == false) {
	include_once(WUO_ROOT . '/install.php');
	die();
}

$time_start = microtime(true); // Засекаем время начала выполнения скрипта

header('Content-Type: text/html; charset=utf-8');

/* Загружаем классы */
include_once(WUO_ROOT . '/class/singleton.php');
include_once(WUO_ROOT . '/class/database.php'); // Класс работы с БД
include_once(WUO_ROOT . '/class/sql.php'); // Класс работы с БД
include_once(WUO_ROOT . '/class/config.php'); // Класс настроек
include_once(WUO_ROOT . '/class/users.php'); // Класс работы с пользователями

/* Загружаем все что нужно для работы движка */
include_once(WUO_ROOT . '/inc/connect.php'); // Соединяемся с БД
include_once(WUO_ROOT . '/inc/config.php'); // Подгружаем настройки из БД, получаем заполненый класс $cfg
include_once(WUO_ROOT . '/inc/functions.php'); // Загружаем функции
include_once(WUO_ROOT . '/inc/login.php'); // Создаём пользователя $user

/* Если указан маршрут, то подключаем указанный в маршруте скрипт и выходим */
if (isset($_GET['route'])) {
	$uri = $_SERVER['REQUEST_URI'];

	// Удаляем лишнее
	if (strpos($uri, '/route') === 0) {
		$uri = substr($uri, 6);
	} else {
		$pos = strpos($uri, '?route=');
		if ($pos) {
			$uri = substr($uri, $pos + 7);
		}
	}

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
		// Загружаем необходимые классы
		include_once(WUO_ROOT . '/class/employees.php'); // Класс работы с профилем пользователя
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

/* Загружаем классы */
include_once(WUO_ROOT . '/class/mod.php'); // Класс работы с модулями
include_once(WUO_ROOT . '/class/cconfig.php'); // Класс работы с пользовательскими настройками
include_once(WUO_ROOT . '/class/class.phpmailer.php'); // Класс управления почтой
include_once(WUO_ROOT . '/class/menu.php'); // Класс работы с меню
include_once(WUO_ROOT . '/inc/autorun.php'); // Запускаем сторонние скрипты

/* Инициализируем заполнение меню */
$gmenu = new Tmenu();
$gmenu->GetFromFiles(WUO_ROOT . '/inc/menu');

$content_page = (isset($_GET['content_page'])) ? $_GET['content_page'] : 'home';

// Загружаем и выполняем сначала /modules/$content_page.php, затем /controller/client/themes/$cfg->theme/$content_page.php
// Если таких файлов нет, то выполняем /controller/client/themes/$cfg->theme/home.php
if (!is_file(WUO_ROOT . "/controller/client/themes/$cfg->theme/$content_page.php")) {
	$content_page = 'home';
	$err[] = 'Вы попытались открыть несуществующий раздел!';
}

// Если есть модуль, то загружаем.
if (is_file(WUO_ROOT . "/modules/$content_page.php")) {
	include_once(WUO_ROOT . "/modules/$content_page.php");
}

// Загружаем главный файл темы, который разруливает что отображать на экране
include_once(WUO_ROOT . "/controller/client/themes/$cfg->theme/index.php");

// Запускаем сторонние скрипты
include_once(WUO_ROOT . '/inc/footerrun.php');

unset($gmenu);
