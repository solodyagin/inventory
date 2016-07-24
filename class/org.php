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
	 * @global type $sqlcn
	 * @param type $id
	 */

	function GetById($id) {
		global $sqlcn;
		$result = $sqlcn->ExecuteSQL("SELECT * FROM org WHERE id = '$id'")
				or die('Неверный запрос Torgs.GetById: ' . mysqli_error($sqlcn->idsqlconnection));
		while ($row = mysqli_fetch_array($result)) {
			$this->id = $row['sid'];
			$this->name = $row['name'];
			$this->picmap = $row['picmap'];
			$this->active = $row['active'];
		}
	}

}
