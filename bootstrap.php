<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

$err = []; # Массив с сообщениями об ошибках для показа пользователю при генерации страницы
$ok = [];  # Массив с информационными сообщениями для показа пользователю при генерации страницы

# Некоторые установки
date_default_timezone_set('Europe/Moscow'); # Временная зона по умолчанию

# Если нет файла конфигурации, то запускаем инсталлятор
if (!is_file(WUO_ROOT . '/config.php')) {
	header('Location: install/index.php');
	die();
}

$time_start = microtime(true); # Засекаем время начала выполнения скрипта

/**
 * Функция автоматической загрузки классов
 * @param type $class
 * @return boolean
 */
function __autoload($class) {
	$arr = explode('_', $class);
	if (empty($arr[1])) {
		$folder = 'classes';
	} else {
		switch (strtolower($arr[0])) {
			case 'controller':
				$folder = 'app/controllers';
				break;
			case 'model':
				$folder = 'app/models';
				break;
			case 'view':
				$folder = 'app/views';
				break;
		}
	}
	$filename = WUO_ROOT . "/$folder/" . strtolower($class) . '.php';
	if (!file_exists($filename)) {
		return false;
	}
	require_once $filename;
}

# Получаем настройки из файла конфигурации
$cfg = Config::getInstance();
$cfg->loadFromFile();

# Если активен режим отладки, то показываем все ошибки и предупреждения
if ($cfg->debug) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}

# Задаём обработчик исключений
set_exception_handler(function ($ex) {
	$cfg = Config::getInstance();
	switch (get_class($ex)) {
		case 'DBException':
			$pr = $ex->getPrevious();
			die(($pr && $cfg->debug) ? $ex->getMessage() . ': ' . $pr->getMessage() : $ex->getMessage());
			break;
		default:
			throw $ex;
	}
});

# Получаем настройки из базы
$cfg->loadFromDB();

# Загружаем все что нужно для работы движка
include_once WUO_ROOT . '/inc/functions.php'; # Загружаем функции

# Аутентифицируем пользователя по кукам
$user = User::getInstance();
$user->loginByCookie();

/**
 * Если указан маршрут, то подключаем указанный в маршруте скрипт и выходим
 * TODO: Является анахронизмом, надо выпилить.
 */
$cfg = Config::getInstance();
$uri = $_SERVER['REQUEST_URI'];
if (strpos($uri, $cfg->rewrite_base) === 0) {
	$uri = substr($uri, strlen($cfg->rewrite_base));
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
		include_once WUO_ROOT . $route;
	} else {
		die("На сервере отсутствует указанный путь '$route'");
	}
	exit();
}

# Загружаем сторонние классы
include_once WUO_ROOT . '/libs/class.phpmailer.php'; # Класс управления почтой

# Запускаем сторонние скрипты
include_once WUO_ROOT . '/inc/autorun.php';

# Инициализируем заполнение меню
$gmenu = new Menu();
$gmenu->GetFromFiles(WUO_ROOT . '/inc/menu');

# Запускаем маршрутизатор
Router::start();
