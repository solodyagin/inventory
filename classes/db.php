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

class DB {

	private static $instance = null;

	public static function getInstance() {
		global $mysql_host, $mysql_user, $mysql_pass, $mysql_base, $mysql_char;
		if (self::$instance === null) {
			$opt = array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => true,
				PDO::ATTR_STATEMENT_CLASS => array('myPDOStatement'),
			);
			$dsn = "mysql:host={$mysql_host};dbname={$mysql_base};charset={$mysql_char}";
			self::$instance = new PDO($dsn, $mysql_user, $mysql_pass, $opt);
		}
		return self::$instance;
	}

	public static function __callStatic($method, $args) {
		return call_user_func_array(array(self::getInstance(), $method), $args);
	}

	final private function __construct() {
		//*
	}

	final private function __clone() {
		//*
	}

	final private function __wakeup() {
		//*
	}

}

class myPDOStatement extends PDOStatement {

	function execute($data = array()) {
		if (count($data) > 0) {
			parent::execute($data);
		} else {
			parent::execute();
		}
		return $this;
	}

}

class DBException extends Exception {

}
