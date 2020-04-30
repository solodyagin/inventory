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
		try {
			$sql = 'select * from config_common where nameparam = :nameparam';
			$row = DB::prepare($sql)->execute([':nameparam' => $nameparam])->fetch();
			if ($row) {
				$resz = $row['valueparam'];
			} else {
				$sql = "insert into config_common (nameparam, valueparam) values (:nameparam, '')";
				DB::prepare($sql)->execute([':nameparam' => $nameparam]);
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
		try {
			$sql = 'select * from config_common where nameparam = :nameparam';
			$row = DB::prepare($sql)->execute([':nameparam' => $nameparam])->fetch();
			if ($row) {
				$sql = 'update config_common set valueparam = :valueparam where nameparam = :nameparam';
			} else {
				$sql = 'insert into config_common (nameparam, valueparam) values (:nameparam, :valueparam)';
			}
			DB::prepare($sql)->execute([':nameparam' => $nameparam, ':valueparam' => $valueparam]);
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения CConfig.SetByParam', 0, $ex);
		}
	}

}
