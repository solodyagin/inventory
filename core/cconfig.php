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

/**
 * Класс для работы с пользовательскими настройками
 */
class cconfig {

	/** Получение значения хранимого параметра по имени параметра
	 *
	 * @param type $nameparam - имя параметра
	 * @return type
	 */
	public function getByParam($nameparam) {
		$resz = '';
		try {
			$sql = 'select * from config_common where nameparam = :nameparam';
			$row = db::prepare($sql)->execute([':nameparam' => $nameparam])->fetch();
			if ($row) {
				$resz = $row['valueparam'];
			} else {
				$sql = "insert into config_common (nameparam, valueparam) values (:nameparam, '')";
				db::prepare($sql)->execute([':nameparam' => $nameparam]);
			}
		} catch (PDOException $ex) {
			throw new dbexception('Ошибка выполнения cconfig.getByParam', 0, $ex);
		}
		return $resz;
	}

	/** Установить значение хранимого параметра
	 *
	 * @param type $nameparam - название параметра
	 * @param type $valueparam - значение параметра
	 */
	public function setByParam($nameparam, $valueparam) {
		try {
			$sql = 'select * from config_common where nameparam = :nameparam';
			$row = db::prepare($sql)->execute([':nameparam' => $nameparam])->fetch();
			if ($row) {
				$sql = 'update config_common set valueparam = :valueparam where nameparam = :nameparam';
			} else {
				$sql = 'insert into config_common (nameparam, valueparam) values (:nameparam, :valueparam)';
			}
			db::prepare($sql)->execute([':nameparam' => $nameparam, ':valueparam' => $valueparam]);
		} catch (PDOException $ex) {
			throw new dbexception('Ошибка выполнения cconfig.setByParam', 0, $ex);
		}
	}

}
