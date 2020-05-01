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

class user extends baseuser {

	use singleton;

	// Пользователь вошёл?
	private $is_logged = false;

	/**
	 * Аутентификация SQL
	 * @param string $login
	 * @param string $password
	 * @return boolean
	 */
	public function loginByDB($login, $password) {
		$cfg = config::getInstance();
		$this->is_logged = false;
		try {
			switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
				case 'mysql':
					$sql = <<<SQL
select
	p.*,
	u.*,
	u.id sid
from users u
	left join users_profile p on p.usersid = u.id
where u.login = :login and
	u.password = sha1(concat(sha1(:pass), u.salt))
SQL;
					break;
				case 'pgsql':
					$sql = <<<SQL
select
	p.*,
	u.*,
	u.id sid
from users u
	left join users_profile p on p.usersid = u.id
where u.login = :login and
	u.password = sha1(concat(sha1(:pass), u.salt::text))
SQL;
					break;
			}
			$row = db::prepare($sql)->execute([':login' => $login, ':pass' => $password])->fetch();
			if ($row) {
				$this->is_logged = true;
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
				$this->post = $row['post'];
			}
		} catch (PDOException $ex) {
			throw new dbexception('Ошибка при получении данных пользователя', 0, $ex);
		}
		// Устанавливаем Cookie
		if ($this->is_logged) {
			setcookie("inventory_{$cfg->inventory_id}", $this->randomid, strtotime('+30 days'), '/');
		}
		return $this->is_logged;
	}

	/**
	 * Аутентифицирует по кукам
	 * @return boolean
	 */
	public function loginByCookie() {
		$cfg = config::getInstance();
		$this->randomid = filter_input(INPUT_COOKIE, "inventory_{$cfg->inventory_id}");
		$this->is_logged = !empty($this->randomid) && $this->getByRandomId($this->randomid);
		if ($this->is_logged) {
			$this->updateLastDt($this->id); // Обновляем дату последнего входа пользователя
			setcookie("inventory_{$cfg->inventory_id}", $this->randomid, strtotime('+30 days'), '/'); // Устанавливаем Cookie
		} else {
			setcookie("inventory_{$cfg->inventory_id}", '', 1, '/'); // Удаляем cookie
		}
		return $this->is_logged;
	}

	public function logout() {
		$cfg = config::getInstance();
		$this->is_logged = false;
		$this->id = '';
		$this->randomid = '';
		// Удаляем cookie
		setcookie("inventory_{$cfg->inventory_id}", '', 1, '/');
//		foreach ($_COOKIE as $key => $value) {
//			setcookie($key, '', 1, '/');
//		}
	}

	/**
	 * Пользователь аутентифицирован?
	 * @return boolean
	 */
	public function isLogged() {
		return ($this->is_logged);
	}

}
