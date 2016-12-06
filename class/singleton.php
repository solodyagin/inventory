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

class Singleton {

	private static $_instances = array();

	public static function getInstance() {
		$class = get_called_class();
		if (!isset(self::$_instances[$class])) {
			self::$_instances[$class] = new static();
		}
		return self::$_instances[$class];
	}

	public function __get($property) {
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}

	public function __set($property, $value) {
		if (property_exists($this, $property)) {
			$this->$property = $value;
		} else {
			throw new Exception("Undefined property $property referenced");
		}
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
