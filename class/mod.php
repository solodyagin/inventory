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

class Tmod {

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
			$cnt = DB::prepare($sql)->execute(array(':modname' => "modulename_$name"))->fetchColumn();
			if ($cnt == 0) {
				// записываем что такой модуль вообще есть, но не активен
				$sql = "INSERT INTO config_common (id, nameparam, valueparam) VALUES (null, :modname, '0')";
				DB::prepare($sql)->execute(array(':modname' => "modulename_$name"));

				// записываем его $comment
				$sql = "INSERT INTO config_common (id, nameparam, valueparam) VALUES (null, :modcomment, :comment)";
				DB::prepare($sql)->execute(array(':modcomment' => "modulecomment_$name", ':comment' => $comment));

				// записываем его $copy
				$sql = 'INSERT INTO config_common (id, nameparam, valueparam) VALUES (null, :modcopy, :copy)';
				DB::prepare($sql)->execute(array(':modcopy' => "modulecopy_$name", ':copy' => $copy));
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Tmod.Register', 0, $ex);
		}
	}

	/**
	 * Отменяет регистрацию модуля
	 * @param string $name
	 */
	function UnRegister($name) {
		try {
			$sql = 'DELETE FROM config_common WHERE nameparam = :modname';
			DB::prepare($sql)->execute(array(':modname' => "modulename_$name"));

			$sql = 'DELETE FROM config_common WHERE nameparam = :modcomment';
			DB::prepare($sql)->execute(array(':modcomment' => "modulecomment_$name"));

			$sql = 'DELETE FROM config_common WHERE nameparam = :modcopy';
			DB::prepare($sql)->execute(array(':modcopy' => "modulecopy_$name"));
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Tmod.UnRegister', 0, $ex);
		}
	}

	/**
	 * Активирует модуль в системе 
	 * @param string $name
	 */
	function Activate($name) {
		try {
			$sql = "UPDATE config_common SET valueparam = '1' WHERE nameparam = :modname";
			DB::prepare($sql)->execute(array(':modname' => "modulename_$name"));
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Tmod.Activate', 0, $ex);
		}
	}

	/**
	 * Деактивирует модуль в системе
	 * @param string $name
	 */
	function DeActivate($name) {
		try {
			$sql = "UPDATE config_common SET valueparam = '0' WHERE nameparam = :modname";
			DB::prepare($sql)->execute(array(':modname' => "modulename_$name"));
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Tmod.DeActivate', 0, $ex);
		}
	}

	/**
	 * проверяем включен модуль или нет?
	 * @param string $name
	 * @return integer
	 */
	function IsActive($name) {
		$active = 0;
		try {
			$sql = 'SELECT * FROM config_common WHERE nameparam = :modname';
			$row = DB::prepare($sql)->execute(array(':modname' => "modulename_$name"))->fetch();
			if ($row) {
				$active = $row['valueparam'];
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Tmod.IsActive', 0, $ex);
		}
		return $active;
	}

}
