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

// Функция автоматической загрузки классов
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
 * @global boolean $debug
 * @param Exception $ex
 * @throws Exception
 */
function exception_handler($ex) {
	global $debug;
	switch (get_class($ex)) {
		case 'DBException':
			$pr = $ex->getPrevious();
			die(($pr && $debug) ? $ex->getMessage() . ': ' . $pr->getMessage() : $ex->getMessage());
			break;
		default:
			throw $ex;
	}
}

set_exception_handler('exception_handler');
