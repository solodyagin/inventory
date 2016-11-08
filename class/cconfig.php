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

class Tcconfig {

	/** Получение значения хранимого параметра по имени параметра
	 * 
	 * @param type $nameparam - имя параметра
	 * @return type
	 */
	function GetByParam($nameparam) {
		$resz = '';
		$sql = 'SELECT * FROM config_common WHERE nameparam = :nameparam';
		$row = DB::prepare($sql)->execute(array(':nameparam' => $nameparam))->fetch();
		if ($row) {
			$resz = $row['valueparam'];
		} else {
			$sql = "INSERT INTO config_common (nameparam, valueparam) VALUES (:nameparam, '')";
			DB::prepare($sql)->execute(array(':nameparam' => $nameparam));
		}
		return $resz;
	}

	/** Установить значение хранимого параметра
	 * 
	 * @param type $nameparam - название параметра
	 * @param type $valueparam - значение параметра
	 */
	function SetByParam($nameparam, $valueparam) {
		$sql = 'SELECT * FROM config_common WHERE nameparam = :nameparam';
		$row = DB::prepare($sql)->execute(array(':nameparam' => $nameparam))->fetch();
		if ($row) {
			$sql = 'UPDATE config_common SET valueparam = :valueparam WHERE nameparam = :nameparam';
		} else {
			$sql = 'INSERT INTO config_common (nameparam, valueparam) VALUES (:nameparam, :valueparam)';
		}
		DB::prepare($sql)->execute(array(':nameparam' => $nameparam, ':valueparam' => $valueparam));
	}

}
