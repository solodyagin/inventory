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
 * Класс для работы с модулями
 */
class Mod {

	var $id; // уникальный идентификатор
	var $name; // наименование модуля
	var $comment; // краткое описание модуля
	var $copy; // какие-нибудь копирайты, например автор, ссылка на сайт автора и т.п.
	var $active; // 1 - включен, 0 - выключен

	/**
	 * Регистрируем модуль в системе
	 * @param string $name
	 * @param string $comment
	 * @param string $copy
	 */
	function Register($name, $comment, $copy) {
		try {
			$sql = 'select count(*) from config_common where nameparam = :modname';
			$cnt = DB::prepare($sql)->execute([':modname' => "modulename_$name"])->fetchColumn();
			if ($cnt == 0) {
				// записываем что такой модуль вообще есть, но не активен
				$sql = "insert into config_common (nameparam, valueparam) values (:modname, '0')";
				DB::prepare($sql)->execute([':modname' => "modulename_$name"]);

				// записываем его $comment
				$sql = "insert into config_common (nameparam, valueparam) values (:modcomment, :comment)";
				DB::prepare($sql)->execute([':modcomment' => "modulecomment_$name", ':comment' => $comment]);

				// записываем его $copy
				$sql = 'insert into config_common (nameparam, valueparam) values (:modcopy, :copy)';
				DB::prepare($sql)->execute([':modcopy' => "modulecopy_$name", ':copy' => $copy]);
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Mod.Register', 0, $ex);
		}
	}

	/**
	 * Отменяет регистрацию модуля
	 * @param string $name
	 */
	function UnRegister($name) {
		try {
			$sql = 'delete from config_common where nameparam = :modname';
			DB::prepare($sql)->execute([':modname' => "modulename_$name"]);

			$sql = 'delete from config_common where nameparam = :modcomment';
			DB::prepare($sql)->execute([':modcomment' => "modulecomment_$name"]);

			$sql = 'delete from config_common where nameparam = :modcopy';
			DB::prepare($sql)->execute([':modcopy' => "modulecopy_$name"]);
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Mod.UnRegister', 0, $ex);
		}
	}

	/**
	 * Активирует модуль в системе
	 * @param string $name
	 */
	function Activate($name) {
		try {
			$sql = "update config_common set valueparam = '1' where nameparam = :modname";
			DB::prepare($sql)->execute([':modname' => "modulename_$name"]);
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Mod.Activate', 0, $ex);
		}
	}

	/**
	 * Деактивирует модуль в системе
	 * @param string $name
	 */
	function DeActivate($name) {
		try {
			$sql = "update config_common set valueparam = '0' where nameparam = :modname";
			DB::prepare($sql)->execute([':modname' => "modulename_$name"]);
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Mod.DeActivate', 0, $ex);
		}
	}

	/**
	 * Проверяем включен модуль или нет?
	 * @param string $name
	 * @return boolean
	 */
	function IsActive($name) {
		$active = false;
		try {
			$sql = 'select * from config_common where nameparam = :modname';
			$row = DB::prepare($sql)->execute([':modname' => "modulename_$name"])->fetch();
			if ($row) {
				$active = ($row['valueparam'] == '1');
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Mod.IsActive', 0, $ex);
		}
		return $active;
	}

}
