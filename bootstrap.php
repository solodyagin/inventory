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

// Запрещаем прямой вызов скрипта
defined('SITE_EXEC') or die('Доступ запрещён');

$err = []; // Массив с сообщениями об ошибках для показа пользователю при генерации страницы
$ok = []; // Массив с информационными сообщениями для показа пользователю при генерации страницы

// Некоторые установки
date_default_timezone_set('Europe/Moscow'); // Временная зона по умолчанию

// Если нет файла конфигурации, то запускаем инсталлятор
if (!is_file(SITE_ROOT . '/app/config.php')) {
	header('Location: install/index.php');
	die();
}

$time_start = microtime(true); // Засекаем время начала выполнения скрипта

use PDO;
use PDOException;
use core\config;
use core\user;
use core\db;
use core\dbexception;
use core\utils;
use core\menu;
use core\router;

// Подключаем Composer
require SITE_ROOT . '/vendor/autoload.php';

// Получаем настройки из файла конфигурации
$cfg = config::getInstance();
$cfg->loadFromFile();

// Задаём обработчик исключений
error_reporting(E_ALL);
set_error_handler(function ($level, $message, $file, $line) {
	if (error_reporting() !== 0) { // to keep the @ operator working
		throw new \ErrorException($message, 0, $level, $file, $line);
	}
});
set_exception_handler(function ($exception) {
	$code = $exception->getCode();
	if ($code != 404) {
		$code = 500;
	}
	http_response_code($code);
	$class = get_class($exception);
	$message = $exception->getMessage();
	if ($class == 'dbexception') {
		$message .= ': ' . $exception->getPrevious()->getMessage();
	}
	$cfg = config::getInstance();
	if ($cfg->debug) {
		echo <<<TEXT
<h1>Fatal error</h1>
<p>Uncaught exception: "$class"</p>
<p>Message: "$message"</p>
<p>Stack trace:<pre>{$exception->getTraceAsString()}</pre></p>
<p>Thrown in "{$exception->getFile()}" on line {$exception->getLine()}</p>
TEXT;
	} else {
		ini_set('error_log', SITE_ROOT . '/logs/' . date('Y-m-d') . '.txt');
		$log = <<<TEXT
Uncaught exception: "$class" with message "$message"
Stack trace: {$exception->getTraceAsString()}
Thrown in "{$exception->getFile()}" on line {$exception->getLine()}
--
TEXT;
		error_log($log);
	}
});

// Добавляем поле "inventory_id"
try {
	$bytes = bin2hex(random_bytes(10));
	switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
		case 'mysql':
			$sql = "select count(*) cnt from information_schema.columns where table_name='config' and column_name='inventory_id'";
			$row = db::prepare($sql)->execute()->fetch();
			$cnt = ($row) ? $row['cnt'] : 0;
			if ($cnt == 0) {
				$sql = "alter table config add column inventory_id varchar(20) not null default '$bytes'";
				db::prepare($sql)->execute();
			}
			break;
		case 'pgsql':
			$sql = "alter table config add column if not exists inventory_id varchar(20) not null default '$bytes'";
			db::prepare($sql)->execute();
			break;
	}
} catch (PDOException $ex) {
	throw new dbexception('Ошибка при добавлении поля "inventory_id"', 0, $ex);
}

// Получаем настройки из базы
$cfg->loadFromDB();

// Обновляем БД
if (strtotime($cfg->version) < strtotime(SITE_VERSION)) {
	try {
		if ($cfg->version == '2020-04-20') {
			db::prepare('update config set theme = :theme')->execute([':theme' => 'cerulean']);
			$cfg->theme = 'cerulean'; // Применение темы
		}
		db::prepare('update config set version = :version')->execute([':version' => SITE_VERSION]);
	} catch (PDOException $ex) {
		throw new dbexception('Ошибка обновления БД', 0, $ex);
	}
}

// Аутентифицируем пользователя по кукам
$user = user::getInstance();
$user->loginByCookie();


/**
 * Если указан маршрут, то подключаем указанный в маршруте скрипт и выходим
 * TODO: Является анахронизмом, надо выпилить.
 */
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
	if ((!preg_match('#^(/deprecated)|(/inc)#', $route)) || (strpos($route, '..') !== false)) {
		die("Запрещён доступ к '$route'");
	}
	// Подключаем запрашиваемый скрипт
	if (is_file(SITE_ROOT . $route)) {
		// Разрешаем доступ только выполнившим вход пользователям
		if ($user->id == '') {
			die('Доступ ограничен');
		}
		include_once SITE_ROOT . $route;
	} else {
		die("На сервере отсутствует указанный путь '$route'");
	}
	exit();
}


// Загружаем сторонние классы
include_once SITE_ROOT . '/vendor/phpmailer/class.phpmailer.php'; // Класс управления почтой

/*
 * смотрим почту в очереди и отправляем одно письмо за раз
 * после чего очередь сокращаем на 1 письмо
 */
try {
	$row = db::prepare('select * from mailq limit 1')->execute()->fetch();
	if ($row) {
		utils::mailq($row['to'], $row['title'], $row['btxt']);
		try {
			db::prepare('delete from mailq where id = :id')->execute([':id' => $row['id']]);
		} catch (PDOException $ex) {
			$err[] = 'Не получилось удалить сообщение из очереди ' . $ex->getMessage();
		}
	}
} catch (PDOException $ex) {
	$err[] = 'Не получилось прочитать очередь сообщений ' . $ex->getMessage();
}

// Инициализируем заполнение меню
$gmenu = new menu();
$gmenu->getFromFiles(SITE_ROOT . '/inc/menu');

// Маршрутизация
$router = router::getInstance();

// Добавляем маршруты
$router->add('/?', ['controller' => 'main', 'action' => 'index']);

// Добавляем в таблицу маршрутов короткие ссылки
$router->add('/{controller}/?');
$router->add('/{controller}/{id:\d+}');
$router->add('/{controller}/{action}'); // например, /account/login

// Добавляем маршруты для модулей
$router->add('/{module}/{controller}/'); // например, /somemodule/controller/
$router->add('/{module}/{controller}/{id:\d+}'); // например, /somemodule/controller/1
$router->add('/{module}/{controller}/{action}'); // например, /somemodule/controller/index

// Запускаем маршрутизатор
$router->dispatch();
