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
	 */

	function Add() {
		$sql = <<<TXT
INSERT INTO users_profile
       (id, usersid, faza, code, enddate, post)
VALUES (NULL, :usersid, :faza, :code, :enddate, :post) 
TXT;
		try {
			DB::prepare($sql)->execute(array(
				':usersid' => $this->usersid,
				':faza' => $this->faza,
				':code' => $this->code,
				':enddate' => $this->enddate,
				':post' => $this->post
			));
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Temployees.Add', 0, $ex);
		}
	}

	/**
	 * Обновляем профиль работника с текущими данными (все что заполнено)
	 */
	function Update() {
		$sql = <<<TXT
UPDATE users_profile
SET    fio = :fio, faza = :faza, code = :code, enddate = :enddate, post = :post
WHERE  code = :code 
TXT;
		try {
			DB::prepare($sql)->execute(array(
				':fio' => $this->fio,
				':faza' => $this->faza,
				':code' => $this->code,
				':enddate' => $this->enddate,
				':post' => $this->post
			));
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Temployees.Update', 0, $ex);
		}
	}

	/**
	 * Обновляем профиль работника с текущими данными (все что заполнено)
	 */
	function GetByERPCode() {
		$sql = 'SELECT * FROM users_profile WHERE code = :code';
		try {
			$row = DB::prepare($sql)->execute(array(':code' => $this->code))->fetch();
			if ($row) {
				$this->id = $row['id'];
				$this->usersid = $row['usersid'];
				$this->fio = $row['fio'];
				$this->faza = $row['faza'];
				$this->enddate = $row['enddate'];
				$this->post = $row['post'];
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Temployees.GetByERPCode', 0, $ex);
		}
	}

	/**
	 * А есть ли такой в базе (проверка по ERPCode. Если есть - возврат 1, иначе 0
	 * @param type $TERPCode
	 * @return boolean
	 */
	function EmployeesYetByERPCode($TERPCode) {
		$sql = 'SELECT * FROM users_profile WHERE code = :code';
		try {
			$row = DB::prepare($sql)->execute(array(':code' => $TERPCode))->fetch();
			if ($row) {
				return true;
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Temployees.EmployeesYetByERPCode', 0, $ex);
		}
		return false;
	}

}
