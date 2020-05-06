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

namespace core;

class model {

	private $name;

	public function __construct() {
		$modelName = get_class($this);
		$arr = explode('_', $modelName);
		$this->name = strtolower($arr[1]);
	}

	public function getData() {
		
	}

}
