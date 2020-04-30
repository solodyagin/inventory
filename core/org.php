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

class Org {

	var $id; // идентификатор организации
	var $name; // наименование организации
	var $picmap; // файл картинки
	var $active; // 1 - активна, 0 - помечена на удаление

	/**
	 * Получить данные об организации по идентификатору
	 * @param type $id
	 */
	function GetById($id) {
		$sql = 'select * from org where id = :id';
		try {
			$row = DB::prepare($sql)->execute([':id' => $id])->fetch();
			if ($row) {
				$this->id = $row['sid'];
				$this->name = $row['name'];
				$this->picmap = $row['picmap'];
				$this->active = $row['active'];
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Org.GetById', 0, $ex);
		}
	}

}
