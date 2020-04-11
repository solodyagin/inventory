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

class User extends BaseUser {

	use Singleton;

	/* Пользователь вошёл ? */
	private $is_logged = false;

	/**
	 * Аутентификация SQL
	 * @param string $login
	 * @param string $password
	 * @return boolean
	 */
	function loginByDB($login, $password) {
		$this->is_logged = false;
		try {
			switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
				case 'mysql':
					$sql = <<<SQL
SELECT	p.*, u.*, u.id sid
FROM	users u
	LEFT JOIN users_profile p ON p.usersid = u.id
WHERE	u.login = :login AND
		u.password = SHA1(CONCAT(SHA1(:pass), u.salt))
SQL;
					break;
				case 'pgsql':
$sql = <<<SQL
SELECT	p.*, u.*, u.id sid
FROM	users u
	LEFT JOIN users_profile p ON p.usersid = u.id
WHERE	u.login = :login AND
		u.password = SHA1(CONCAT(SHA1(:pass), u.salt::text))
SQL;
					break;
			}
			$row = DB::prepare($sql)->execute([':login' => $login, ':pass' => $password])->fetch();
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
			throw new DBException('Ошибка при получении данных пользователя', 0, $ex);
		}
		/* Устанавливаем Cookie */
		if ($this->is_logged) {
			setcookie('inventory_id', $this->randomid, strtotime('+30 days'), '/');
		}
		return $this->is_logged;
	}

	/**
	 * Аутентифицирует по кукам
	 * @return boolean
	 */
	function loginByCookie() {
		$this->randomid = filter_input(INPUT_COOKIE, 'inventory_id');
		$this->is_logged = !empty($this->randomid) && $this->getByRandomId($this->randomid);
		if ($this->is_logged) {
			$this->UpdateLastdt($this->id); # Обновляем дату последнего входа пользователя
			setcookie('inventory_id', $this->randomid, strtotime('+30 days'), '/'); # Устанавливаем Cookie
		} else {
			setcookie('inventory_id', '', 1, '/'); # Удаляем cookie
		}
		return $this->is_logged;
	}

	function logout() {
		$this->is_logged = false;
		$this->id = '';
		$this->randomid = '';
		/* Удаляем cookie */
		setcookie('inventory_id', '', 1, '/');
//		foreach ($_COOKIE as $key => $value) {
//			setcookie($key, '', 1, '/');
//		}
	}

	/**
	 * Пользователь аутентифицирован?
	 * @return boolean
	 */
	function isLogged() {
		return ($this->is_logged);
	}

}
