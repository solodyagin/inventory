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
		if (self::$instance === null) {
			$cfg = Config::getInstance();
			$opt = array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => true,
				PDO::ATTR_STATEMENT_CLASS => array('myPDOStatement'),
			);
			$dsn = "mysql:host={$cfg->db_host};dbname={$cfg->db_name};charset={$cfg->db_char}";
			self::$instance = new PDO($dsn, $cfg->db_user, $cfg->db_pass, $opt);
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
