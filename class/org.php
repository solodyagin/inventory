<?php

/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

class Torgs {

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
		} catch (PDOException $e) {
			throw new Exception('Неверный запрос Torgs.GetById: ' . $e->getMessage());
		}
	}

}
