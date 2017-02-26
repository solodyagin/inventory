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

class Org {

	var $id; // идентификатор организации
	var $name; // наименование организации
	var $picmap; // файл картинки
	var $active; // 1 - активна, 0 - помечена на удаление   

	/**
	 * Получить данные о пользователе по идентификатору
	 * @param type $id
	 */

	function GetById($id) {
		$sql = 'SELECT * FROM org WHERE id = :id';
		try {
			$row = DB::prepare($sql)->execute(array(':id' => $id))->fetch();
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
