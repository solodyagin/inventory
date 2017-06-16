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

/* Получаем настройки из файла конфигурации */
$cfg = Config::getInstance();
$cfg->loadFromFile();

// Если активен режим отладки, то показываем все ошибки и предупреждения
if ($cfg->debug) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}

/* Задаём обработчик исключений */
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

/* Получаем настройки из базы */
$cfg->loadFromDB();

/* Загружаем все что нужно для работы движка */
include_once WUO_ROOT . '/inc/functions.php'; // Загружаем функции

/* Аутентифицируем пользователя по кукам */
$user = User::getInstance();
$user->loginByCookie();
