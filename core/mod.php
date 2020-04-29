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

/* Запрещаем прямой вызов скрипта. */
defined('SITE_EXEC') or die('Доступ запрещён');

/**
 * Класс для работы с модулями
 */
class Mod {

	var $id;  // уникальный идентификатор
	var $name;   // наименование модуля
	var $comment; // краткое описание модуля
	var $copy;   // какие-нибудь копирайты, например автор, ссылка на сайт автора и т.п.
	var $active; // 1 - включен, 0 - выключен

	/**
	 * Регистрируем модуль в системе
	 * @param string $name
	 * @param string $comment
	 * @param string $copy
	 */

	function Register($name, $comment, $copy) {
		try {
			$sql = 'SELECT COUNT(*) FROM config_common WHERE nameparam = :modname';
			$cnt = DB::prepare($sql)->execute([':modname' => "modulename_$name"])->fetchColumn();
			if ($cnt == 0) {
				// записываем что такой модуль вообще есть, но не активен
				$sql = "INSERT INTO config_common (nameparam, valueparam) VALUES (:modname, '0')";
				DB::prepare($sql)->execute([':modname' => "modulename_$name"]);

				// записываем его $comment
				$sql = "INSERT INTO config_common (nameparam, valueparam) VALUES (:modcomment, :comment)";
				DB::prepare($sql)->execute([':modcomment' => "modulecomment_$name", ':comment' => $comment]);

				// записываем его $copy
				$sql = 'INSERT INTO config_common (nameparam, valueparam) VALUES (:modcopy, :copy)';
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
			$sql = 'DELETE FROM config_common WHERE nameparam = :modname';
			DB::prepare($sql)->execute([':modname' => "modulename_$name"]);

			$sql = 'DELETE FROM config_common WHERE nameparam = :modcomment';
			DB::prepare($sql)->execute([':modcomment' => "modulecomment_$name"]);

			$sql = 'DELETE FROM config_common WHERE nameparam = :modcopy';
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
			$sql = "UPDATE config_common SET valueparam = '1' WHERE nameparam = :modname";
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
			$sql = "UPDATE config_common SET valueparam = '0' WHERE nameparam = :modname";
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
			$sql = 'SELECT * FROM config_common WHERE nameparam = :modname';
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
