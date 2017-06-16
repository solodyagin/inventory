<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

/**
 * Класс для работы с пользовательскими настройками
 */
class CConfig {

	/** Получение значения хранимого параметра по имени параметра
	 *
	 * @param type $nameparam - имя параметра
	 * @return type
	 */
	function GetByParam($nameparam) {
		$resz = '';
		$sql = 'SELECT * FROM config_common WHERE nameparam = :nameparam';
		try {
			$row = DB::prepare($sql)->execute(array(':nameparam' => $nameparam))->fetch();
			if ($row) {
				$resz = $row['valueparam'];
			} else {
				$sql = "INSERT INTO config_common (nameparam, valueparam) VALUES (:nameparam, '')";
				DB::prepare($sql)->execute(array(':nameparam' => $nameparam));
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения CConfig.GetByParam', 0, $ex);
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
		try {
			$row = DB::prepare($sql)->execute(array(':nameparam' => $nameparam))->fetch();
			if ($row) {
				$sql = 'UPDATE config_common SET valueparam = :valueparam WHERE nameparam = :nameparam';
			} else {
				$sql = 'INSERT INTO config_common (nameparam, valueparam) VALUES (:nameparam, :valueparam)';
			}
			DB::prepare($sql)->execute(array(':nameparam' => $nameparam, ':valueparam' => $valueparam));
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения CConfig.SetByParam', 0, $ex);
		}
	}

}