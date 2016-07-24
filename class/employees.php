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

class Temployees {

	var $id; // идентификатор 
	var $usersid; // связь с пользователем
	var $faza; // в какой фазе пользователь (например в отпуске)
	var $code; // связь с ERP
	var $enddate; // дата когда фаза кончится
	var $post; // Должность

	/**
	 * Добавляем профиль работника с текущими данными (все что заполнено)
	 * @global type $sqlcn
	 */

	function Add() {
		global $sqlcn;
		$sql = <<<TXT
INSERT INTO users_profile
            (id,usersid,faza,code,enddate,post)
VALUES      (NULL,'$this->usersid','$this->faza','$this->code','$this->enddate','$this->post') 
TXT;
		$sqlcn->ExecuteSQL($sql)
				or die('Неверный запрос Temployees.Add: ' . mysqli_error($sqlcn->idsqlconnection));
	}

	/**
	 * Обновляем профиль работника с текущими данными (все что заполнено)
	 * @global type $sqlcn
	 */
	function Update() {
		global $sqlcn;
		$sql = <<<TXT
UPDATE users_profile
SET    fio = '$this->fio',faza = '$this->faza',code = '$this->code',enddate = '$this->enddate',post = '$this->post'
WHERE  code = '$this->code' 
TXT;
		$sqlcn->ExecuteSQL($sql)
				or die('Неверный запрос Temployees.Update: ' . mysqli_error($sqlcn->idsqlconnection));
	}

	/**
	 * Обновляем профиль работника с текущими данными (все что заполнено)
	 * @global type $sqlcn
	 */
	function GetByERPCode() {
		global $sqlcn;
		$result = $sqlcn->ExecuteSQL("SELECT * FROM users_profile WHERE code = '$this->code'")
				or die('Неверный запрос Temployees.GetByERPCode: ' . mysqli_error($sqlcn->idsqlconnection));
		while ($row = mysqli_fetch_array($result)) {
			$this->id = $row['id'];
			$this->usersid = $row['usersid'];
			$this->fio = $row['fio'];
			$this->faza = $row['faza'];
			$this->enddate = $row['enddate'];
			$this->post = $row['post'];
		}
	}

	/**
	 * А есть ли такой в базе (проверка по ERPCode. Если есть - возврат 1, иначе 0
	 * @global type $sqlcn
	 * @param type $TERPCode
	 * @return boolean
	 */
	function EmployeesYetByERPCode($TERPCode) {
		global $sqlcn;
		$result = $sqlcn->ExecuteSQL("SELECT * FROM users_profile WHERE code = '$TERPCode'")
				or die('Ошибка (EmployeesYetByERPCode): ' . mysqli_error($sqlcn->idsqlconnection));
		while ($row = mysqli_fetch_array($result)) {
			return true;
		}
		return false;
	}

}
