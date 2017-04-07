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

/**
 * Задаём обработчик исключений
 */
set_exception_handler(function ($ex) {
	global $debug;
	switch (get_class($ex)) {
		case 'DBException':
			$pr = $ex->getPrevious();
			die(($pr && $debug) ? $ex->getMessage() . ': ' . $pr->getMessage() : $ex->getMessage());
			break;
		default:
			throw $ex;
	}
});
