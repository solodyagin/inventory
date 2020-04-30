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

//namespace Core;

class DB {

	private static $instance = null;

	public static function getInstance() {
		if (self::$instance === null) {
			$cfg = Config::getInstance();
			$opt = [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => true,
				PDO::ATTR_STATEMENT_CLASS => ['myPDOStatement'],
			];
			if ($cfg->db_driver == 'mysql') {
				$dsn = "{$cfg->db_driver}:host={$cfg->db_host};dbname={$cfg->db_name};charset={$cfg->db_char}";
			} else {
				$dsn = "{$cfg->db_driver}:host={$cfg->db_host};dbname={$cfg->db_name}";
			}
			self::$instance = new PDO($dsn, $cfg->db_user, $cfg->db_pass, $opt);
		}
		return self::$instance;
	}

	public static function __callStatic($method, $args) {
		return call_user_func_array([self::getInstance(), $method], $args);
	}

	final private function __construct() {
		// Override
	}

	final private function __clone() {
		// Override
	}

	final private function __wakeup() {
		// Override
	}

}

class myPDOStatement extends PDOStatement {

	function execute($data = []) {
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
