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
 * Класс для работы с модулями
 */
class mod {

	public $id; // уникальный идентификатор
	public $name; // наименование модуля
	public $comment; // краткое описание модуля
	public $copy; // какие-нибудь копирайты, например автор, ссылка на сайт автора и т.п.
	public $active; // 1 - включен, 0 - выключен

	/**
	 * Регистрируем модуль в системе
	 * @param string $name
	 * @param string $comment
	 * @param string $copy
	 */
	public function register($name, $comment, $copy) {
		try {
			$sql = 'select count(*) from config_common where nameparam = :modname';
			$cnt = db::prepare($sql)->execute([':modname' => "modulename_$name"])->fetchColumn();
			if ($cnt == 0) {
				// записываем что такой модуль вообще есть, но не активен
				$sql = "insert into config_common (nameparam, valueparam) values (:modname, '0')";
				db::prepare($sql)->execute([':modname' => "modulename_$name"]);

				// записываем его $comment
				$sql = "insert into config_common (nameparam, valueparam) values (:modcomment, :comment)";
				db::prepare($sql)->execute([':modcomment' => "modulecomment_$name", ':comment' => $comment]);

				// записываем его $copy
				$sql = 'insert into config_common (nameparam, valueparam) values (:modcopy, :copy)';
				db::prepare($sql)->execute([':modcopy' => "modulecopy_$name", ':copy' => $copy]);
			}
		} catch (PDOException $ex) {
			throw new dbexception('Ошибка выполнения mod.register', 0, $ex);
		}
	}

	/**
	 * Отменяет регистрацию модуля
	 * @param string $name
	 */
	public function unRegister($name) {
		try {
			$sql = 'delete from config_common where nameparam = :modname';
			db::prepare($sql)->execute([':modname' => "modulename_$name"]);

			$sql = 'delete from config_common where nameparam = :modcomment';
			db::prepare($sql)->execute([':modcomment' => "modulecomment_$name"]);

			$sql = 'delete from config_common where nameparam = :modcopy';
			db::prepare($sql)->execute([':modcopy' => "modulecopy_$name"]);
		} catch (PDOException $ex) {
			throw new dbexception('Ошибка выполнения mod.unRegister', 0, $ex);
		}
	}

	/**
	 * Активирует модуль в системе
	 * @param string $name
	 */
	public function activate($name) {
		try {
			$sql = "update config_common set valueparam = '1' where nameparam = :modname";
			db::prepare($sql)->execute([':modname' => "modulename_$name"]);
		} catch (PDOException $ex) {
			throw new dbexception('Ошибка выполнения mod.activate', 0, $ex);
		}
	}

	/**
	 * Деактивирует модуль в системе
	 * @param string $name
	 */
	public function deActivate($name) {
		try {
			$sql = "update config_common set valueparam = '0' where nameparam = :modname";
			db::prepare($sql)->execute([':modname' => "modulename_$name"]);
		} catch (PDOException $ex) {
			throw new dbexception('Ошибка выполнения mod.deActivate', 0, $ex);
		}
	}

	/**
	 * Проверяет: включен модуль или нет?
	 * @param string $name
	 * @return boolean
	 */
	public function isActive($name) {
		$active = false;
		try {
			$sql = 'select * from config_common where nameparam = :modname';
			$row = db::prepare($sql)->execute([':modname' => "modulename_$name"])->fetch();
			if ($row) {
				$active = ($row['valueparam'] == '1');
			}
		} catch (PDOException $ex) {
			throw new dbexception('Ошибка выполнения mod.isActive', 0, $ex);
		}
		return $active;
	}

}
