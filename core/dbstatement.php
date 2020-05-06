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

use PDOStatement;

class dbstatement extends PDOStatement {

	function execute($data = []) {
		if (count($data) > 0) {
			parent::execute($data);
		} else {
			parent::execute();
		}
		return $this;
	}

}
