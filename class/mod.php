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

class Tmod {

	var $id;  // уникальный идентификатор
	var $name;   // наименование модуля
	var $comment; // краткое описание модуля
	var $copy;   // какие-нибудь копирайты, например автор, ссылка на сайт автора и т.п.
	var $active; // 1 - включен, 0 - выключен 

	/**
	 * Регистрируем модуль в системе
	 * @global type $sqlcn
	 * @param type $name
	 * @param type $comment
	 * @param type $copy
	 */

	function Register($name, $comment, $copy) {
		global $sqlcn;
		$modname = 'modulename_' . $name;
		$result = $sqlcn->ExecuteSQL("SELECT * FROM config_common WHERE nameparam='$modname'")
				or die('Неверный запрос Tmod.Register: ' . mysqli_error($sqlcn->idsqlconnection));
		if (mysqli_num_rows($result) == 0) {
			// записываем что такой модуль вообще есть, но не активен
			$sqlcn->ExecuteSQL("INSERT INTO config_common (id, nameparam, valueparam) VALUES (null, '$modname', '0')");
			// записываем его $comment
			$modcomment = 'modulecomment_' . $name;
			$sqlcn->ExecuteSQL("INSERT INTO config_common (id, nameparam, valueparam) VALUES (null, '$modcomment', '$comment')");
			// записываем его $copy
			$modcopy = 'modulecopy_' . $name;
			$sqlcn->ExecuteSQL("INSERT INTO config_common (id, nameparam, valueparam) VALUES (null, '$modcopy', '$copy')");
		}
	}

	/**
	 * Отменяет регистрацию модуля
	 * @global type $sqlcn
	 * @param type $name
	 */
	function UnRegister($name) {
		global $sqlcn;
		$modname = 'modulename_' . $name;
		$sqlcn->ExecuteSQL("DELETE FROM config_common WHERE nameparam = '$modname'")
				or die('Неверный запрос Tmod.UnRegister(1): ' . mysqli_error($sqlcn->idsqlconnection));
		$modcomment = 'modulecomment_' . $name;
		$sqlcn->ExecuteSQL("DELETE FROM config_common WHERE nameparam = '$modcomment'")
				or die('Неверный запрос Tmod.UnRegister(2): ' . mysqli_error($sqlcn->idsqlconnection));
		$modcopy = 'modulecopy_' . $name;
		$sqlcn->ExecuteSQL("DELETE FROM config_common WHERE nameparam = '$modcopy'")
				or die('Неверный запрос Tmod.UnRegister(3): ' . mysqli_error($sqlcn->idsqlconnection));
	}

	/**
	 * Активируем модуль в системе 
	 * @global type $sqlcn
	 * @param type $name
	 */
	function Activate($name) {
		global $sqlcn;
		$modname = 'modulename_' . $name;
		$sqlcn->ExecuteSQL("UPDATE config_common SET valueparam = '1' WHERE nameparam ='$modname'")
				or die('Неверный запрос Tmod.Activate: ' . mysqli_error($sqlcn->idsqlconnection));
	}

	/**
	 * ДеАктивируем модуль в системе
	 * 
	 * @global type $sqlcn
	 * @param type $name
	 */
	function DeActivate($name) {
		global $sqlcn;
		$modname = 'modulename_' . $name;
		$sqlcn->ExecuteSQL("UPDATE config_common SET valueparam = '0' WHERE nameparam = '$modname'")
				or die('Неверный запрос Tmod.DeActivate: ' . mysqli_error($sqlcn->idsqlconnection));
	}

	/**
	 * проверяем включен модуль или нет?
	 * @global type $sqlcn
	 * @param type $name
	 * @return type
	 */
	function IsActive($name) {
		global $sqlcn;
		$modname = 'modulename_' . $name;
		$result = $sqlcn->ExecuteSQL("SELECT * FROM config_common WHERE nameparam = '$modname'")
				or die('Неверный запрос Tmod.IsActive: ' . mysqli_error($sqlcn->idsqlconnection));
		$active = 0;
		// проверяем, а может модуль уже зарегистрирован? Если нет, то только тогда его заносим в базу 
		while ($myrow = mysqli_fetch_array($result)) {
			$active = $myrow['valueparam'];
		}
		return $active;
	}

}
