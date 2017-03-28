<?php

/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

class Model {

	private $name;

	function __construct() {
		$modelName = get_class($this);
		$arr = explode('_', $modelName);
		$this->name = strtolower($arr[1]);
	}

	function getData() {

	}

}
