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

class User {

	var $id; // идентификатор пользователя
	var $randomid; // случайный идентификатор (время от времени может менятся)
	var $orgid; // принадлежность к организации
	var $login; // логин
	var $password; // хешированный пароль
	var $salt; // соль для хеширования пароля
	var $email; // электронная почта
	var $mode; // 0 - пользователь 1- админ
	var $lastdt; // дата и время последнего посещения
	var $active; // 1-не помечен на удаление
	// далее выдергивается из профиля если оный есть по GetById
	var $fio; // фамилия имя отчество
	var $telephonenumber; // телефонный номер (сотовый)
	var $homephone; // телефонный номер (альтернатива)
	var $jpegphoto; // фотография из папки photos
	var $tab_num; // табельный номер
	var $post; // должность

	/**
	 * Проверяем соответствие роли
	 *
	 * Роли:
	 * http://грибовы.рф/wiki/doku.php/основы:доступ:роли
	 *            1="Полный доступ"
	 *            2="Просмотр финансовых отчетов"
	 *            3="Просмотр"
	 *            4="Добавление"
	 *            5="Редактирование"
	 *            6="Удаление"
	 *            7="Отправка СМС"
	 *            8="Манипуляции с деньгами"
	 *            9="Редактирование карт"
	 *
	 * @param string $roles
	 * @return boolean
	 */

	function TestRoles($roles) {
		$sql = "SELECT COUNT(*) FROM usersroles WHERE userid = :id AND role IN ($roles)";
		try {
			return (DB::prepare($sql)->execute(array(':id' => $this->id))->fetchColumn() > 0);
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.TestRoles', 0, $ex);
		}
	}

	/**
	 * Обновляем данные о последнем посещении
	 * @param type $id
	 */
	function UpdateLastdt($id) {
		$lastdt = date('Y-m-d H:i:s');
		$sql = 'UPDATE users SET lastdt = :lastdt WHERE id = :id';
		try {
			DB::prepare($sql)->execute(array(':lastdt' => $lastdt, ':id' => $id));
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.UpdateLastdt', 0, $ex);
		}
	}

	/**
	 * Обновляем данные о текущем пользователе в базу
	 */
	function Update() {
		try {
			$sql = <<<TXT
UPDATE users
SET orgid = :orgid, login = :login, `password` = :password, salt = :salt,
	email = :email, mode = :mode, active = :active
WHERE id = :id
TXT;
			DB::prepare($sql)->execute(array(
				':orgid' => $this->orgid,
				':login' => $this->login,
				':password' => $this->password,
				':salt' => $this->salt,
				':email' => $this->email,
				':mode' => $this->mode,
				':active' => $this->active,
				':id' => $this->id
			));
			$sql = <<<TXT
UPDATE users_profile
SET fio = :fio, telephonenumber = :telephonenumber, homephone = :homephone,
	jpegphoto = :jpegphoto, code = :code, post = :post
WHERE usersid = :userid
TXT;
			DB::prepare($sql)->execute(array(
				':fio' => $this->fio,
				':telephonenumber' => $this->telephonenumber,
				':homephone' => $this->homephone,
				':jpegphoto' => $this->jpegphoto,
				':code' => $this->tab_num,
				':post' => $this->post,
				':userid' => $this->id
			));
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.Update', 0, $ex);
		}
	}

	/**
	 * Получить данные о пользователе по логину
	 * @param type $login
	 */
	function GetByLogin($login) {
		$sql = <<<TXT
SELECT users_profile.*,users.*,users.id AS sid
FROM   users
       INNER JOIN users_profile
               ON users_profile.usersid = users.id
WHERE  users.login = :login
TXT;
		try {
			$row = DB::prepare($sql)->execute(array(':login' => $login))->fetch();
			if ($row) {
				$this->id = $row['sid'];
				$this->randomid = $row['randomid'];
				$this->orgid = $row['orgid'];
				$this->login = $row['login'];
				$this->password = $row['password'];
				$this->salt = $row['salt'];
				$this->email = $row['email'];
				$this->mode = $row['mode'];
				$this->lastdt = $row['lastdt'];
				$this->active = $row['active'];
				$this->telephonenumber = $row['telephonenumber'];
				$this->jpegphoto = $row['jpegphoto'];
				$this->homephone = $row['homephone'];
				$this->fio = $row['fio'];
				$this->tab_num = $row['code'];
				$this->post = $row['post'];
				return true;
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.GetByLogin', 0, $ex);
		}
		return false;
	}

	/**
	 * Получить данные о пользователе по логину и паролю
	 * @param type $login
	 * @param type $pass
	 */
	function GetByLoginPass($login, $pass) {
		$sql = <<<TXT
SELECT users_profile.*,users.*,users.id AS sid
FROM   users
       INNER JOIN users_profile
               ON users_profile.usersid = users.id
WHERE  users.login = :login
       AND users.`password` = SHA1(CONCAT(SHA1(:pass), users.salt))
TXT;
		try {
			$row = DB::prepare($sql)->execute(array(':login' => $login, ':pass' => $pass))->fetch();
			if ($row) {
				$this->id = $row['sid'];
				$this->randomid = $row['randomid'];
				$this->orgid = $row['orgid'];
				$this->login = $row['login'];
				$this->password = $row['password'];
				$this->salt = $row['salt'];
				$this->email = $row['email'];
				$this->mode = $row['mode'];
				$this->lastdt = $row['lastdt'];
				$this->active = $row['active'];
				$this->telephonenumber = $row['telephonenumber'];
				$this->jpegphoto = $row['jpegphoto'];
				$this->homephone = $row['homephone'];
				$this->fio = $row['fio'];
				$this->tab_num = $row['code'];
				$this->post = $row['post'];
				return true;
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.GetByLoginPass', 0, $ex);
		}
		return false;
	}

	/**
	 * Получить данные о пользователе по идентификатору
	 * @param type $id
	 */
	function GetById($id) {
		$sql = <<<TXT
SELECT users_profile.*,users.*,users.id AS sid
FROM   users
       INNER JOIN users_profile
               ON users_profile.usersid = users.id
WHERE  users.id = :id
TXT;
		try {
			$row = DB::prepare($sql)->execute(array(':id' => $id))->fetch();
			if ($row) {
				$this->id = $row['sid'];
				$this->randomid = $row['randomid'];
				$this->orgid = $row['orgid'];
				$this->login = $row['login'];
				$this->password = $row['password'];
				$this->salt = $row['salt'];
				$this->email = $row['email'];
				$this->mode = $row['mode'];
				$this->lastdt = $row['lastdt'];
				$this->active = $row['active'];
				$this->telephonenumber = $row['telephonenumber'];
				$this->jpegphoto = $row['jpegphoto'];
				$this->homephone = $row['homephone'];
				$this->fio = $row['fio'];
				$this->tab_num = $row['code'];
				$this->post = $row['post'];
				return true;
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.GetById', 0, $ex);
		}
		return false;
	}

	/**
	 * Получить данные о пользователе по идентификатору randomid
	 * @param type $randomid
	 * @return boolean
	 */
	function GetByRandomId($randomid) {
		$sql = <<<TXT
SELECT users_profile.*,users.*,users.id AS sid
FROM   users
       INNER JOIN users_profile
               ON users_profile.usersid = users.id
WHERE  users.randomid = :randomid
TXT;
		try {
			$row = DB::prepare($sql)->execute(array(':randomid' => $randomid))->fetch();
			if ($row) {
				$this->id = $row['sid'];
				$this->randomid = $row['randomid'];
				$this->orgid = $row['orgid'];
				$this->login = $row['login'];
				$this->password = $row['password'];
				$this->salt = $row['salt'];
				$this->email = $row['email'];
				$this->mode = $row['mode'];
				$this->lastdt = $row['lastdt'];
				$this->active = $row['active'];
				$this->telephonenumber = $row['telephonenumber'];
				$this->jpegphoto = $row['jpegphoto'];
				$this->homephone = $row['homephone'];
				$this->fio = $row['fio'];
				$this->tab_num = $row['code'];
				$this->post = $row['post'];
				return true;
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.GetByRandomId', 0, $ex);
		}
		return false;
	}

	/**
	 * Получить данные о пользователе по идентификатору. БЕЗ ПРОФИЛЯ
	 * @param type $randomid
	 * @return boolean
	 */
	function GetByRandomIdNoProfile($randomid) {
		$sql = 'SELECT * FROM users WHERE randomid = :randomid';
		try {
			$row = DB::prepare($sql)->execute(array(':randomid' => $randomid))->fetch();
			if ($row) {
				$this->id = $row['id'];
				$this->randomid = $row['randomid'];
				$this->orgid = $row['orgid'];
				$this->login = $row['login'];
				$this->password = $row['password'];
				$this->salt = $row['salt'];
				$this->email = $row['email'];
				$this->mode = $row['mode'];
				$this->lastdt = $row['lastdt'];
				$this->active = $row['active'];
				return true;
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.GetByRandomIdNoProfile', 0, $ex);
		}
		return false;
	}

	/**
	 * Добавляем пользователя с текущими данными
	 * @param string $randomid
	 * @param string $orgid
	 * @param string $login
	 * @param string $pass Открытый пароль
	 * @param string $email
	 * @param string $mode
	 */
	function Add($randomid, $orgid, $login, $pass, $email, $mode) {
		$this->randomid = $randomid;
		$this->orgid = $orgid;
		$this->login = $login;
		// Хешируем пароль
		$this->salt = generateSalt();
		$this->password = sha1(sha1($pass) . $this->salt);
		$this->email = $email;
		$this->mode = $mode;
		$sql = <<<TXT
INSERT INTO users
            (id, randomid, orgid, login, password, salt, email, mode, lastdt, active)
VALUES      (NULL, :randomid, :orgid, :login, :password, :salt, :email,
             :mode, NOW(),
             1)
TXT;
		try {
			DB::prepare($sql)->execute(array(
				':randomid' => $this->randomid,
				':orgid' => $this->orgid,
				':login' => $this->login,
				':password' => $this->password,
				':salt' => $this->salt,
				':email' => $this->email,
				':mode' => $this->mode
			));
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.Add', 0, $ex);
		}

		$zx = new User();

		if ($zx->GetByRandomIdNoProfile($this->randomid)) {
			// добавляю профиль
			$sql = <<<TXT
INSERT INTO users_profile
            (id, usersid, fio, code, telephonenumber, homephone, jpegphoto, post, faza, enddate,
            res1, res2, res3, res4)
VALUES      (NULL, :userid, :fio, :code, :telephonenumber, :homephone, :jpegphoto, :post, '', NOW(),
            '', 0, 0, '0000-00-00 00:00:00')
TXT;
			try {
				DB::prepare($sql)->execute(array(
					':userid' => $zx->id,
					':fio' => $this->fio,
					':code' => $this->tab_num,
					':telephonenumber' => $this->telephonenumber,
					':homephone' => $this->homephone,
					':jpegphoto' => $this->jpegphoto,
					':post' => $this->post
				));
			} catch (PDOException $ex) {
				throw new DBException('Ошибка выполнения User.Add', 0, $ex);
			}
		} else {
			die('Не найден пользователь по randomid User.Add');
		}
	}

	/**
	 * Получить данные о пользователе по коду из профиля
	 * @param type $code
	 * @return boolean
	 */
	function GetByCode($code) {
		$sql = <<<TXT
SELECT users_profile.*,users.*
FROM   users_profile
       INNER JOIN users
               ON users_profile.usersid = users.id
WHERE  users_profile.code = :code
TXT;
		try {
			DB::prepare($sql)->execute(array(':code' => $code))->fetch();
			if ($row) {
				$this->id = $row['usersid'];
				$this->randomid = $row['randomid'];
				$this->orgid = $row['orgid'];
				$this->login = $row['login'];
				$this->password = $row['password'];
				$this->salt = $row['salt'];
				$this->email = $row['email'];
				$this->mode = $row['mode'];
				$this->lastdt = $row['lastdt'];
				$this->active = $row['active'];
				$this->telephonenumber = $row['telephonenumber'];
				$this->jpegphoto = $row['jpegphoto'];
				$this->homephone = $row['homephone'];
				$this->fio = $row['fio'];
				$this->post = $row['post'];
				return true;
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.GetByCode', 0, $ex);
		}
		return false;
	}

}
