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

use PDOException;

class org {

	public $id; // идентификатор организации
	public $name; // наименование организации
	public $picmap; // файл картинки
	public $active; // 1 - активна, 0 - помечена на удаление

	/**
	 * Получить данные об организации по идентификатору
	 * @param type $id
	 */
	function getById($id) {
		try {
			$sql = 'select * from org where id = :id';
			$row = db::prepare($sql)->execute([':id' => $id])->fetch();
			if ($row) {
				$this->id = $row['sid'];
				$this->name = $row['name'];
				$this->picmap = $row['picmap'];
				$this->active = $row['active'];
			}
		} catch (PDOException $ex) {
			throw new dbexception('Ошибка выполнения org.getById', 0, $ex);
		}
	}

}
