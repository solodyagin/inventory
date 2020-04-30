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

class BaseUser {

	public $id; // Идентификатор пользователя
	public $randomid; // Случайный идентификатор (время от времени может меняться)
	public $orgid; // Принадлежность к организации
	public $login; // Логин
	public $password; // Хешированный пароль
	public $salt; // Соль для хеширования пароля
	public $email; // Электронная почта
	public $mode; // 0 - пользователь 1 - администратор
	public $lastdt; // Дата и время последнего посещения
	public $active; // 1 - не помечен на удаление
	public $fio; // Фамилия, Имя, Отчество
	public $telephonenumber; // Номер телефона (сотовый)
	public $homephone; // Номер телефона (альтернатива)
	public $jpegphoto; // Фотография из папки photos
	public $post; // Должность

	/**
	 * Проверяем соответствие прав
	 *
	 * Права:
	 * http://грибовы.рф/wiki/doku.php/основы:доступ:роли
	 *   1="Полный доступ"
	 *   2="Просмотр финансовых отчетов" - не используется
	 *   3="Просмотр"
	 *   4="Добавление"
	 *   5="Редактирование"
	 *   6="Удаление"
	 *   7="Отправка СМС" - не используется
	 *   8="Манипуляции с деньгами" - не используется
	 *   9="Редактирование карт" - не используется
	 *
	 * @param array $roles
	 * @return boolean
	 */
	function TestRights($roles) {
		$sr = implode(',', array_map('intval', $roles));
		$sql = "select count(*) as cnt from usersroles where userid = :id and role in ($sr)";
		try {
			$row = DB::prepare($sql)->execute([':id' => $this->id])->fetch();
			$cnt = ($row) ? $row['cnt'] : 0;
			return $cnt > 0;
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.TestRights', 0, $ex);
		}
	}

	/**
	 * Обновляем данные о последнем посещении
	 * @param type $id
	 */
	function UpdateLastdt($id) {
		$lastdt = date('Y-m-d H:i:s');
		$sql = 'update users set lastdt = :lastdt where id = :id';
		try {
			DB::prepare($sql)->execute([':lastdt' => $lastdt, ':id' => $id]);
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
update users
set orgid = :orgid, login = :login, password = :password, salt = :salt,
	email = :email, mode = :mode, active = :active
where id = :id
TXT;
			DB::prepare($sql)->execute([
				':orgid' => $this->orgid,
				':login' => $this->login,
				':password' => $this->password,
				':salt' => $this->salt,
				':email' => $this->email,
				':mode' => $this->mode,
				':active' => $this->active,
				':id' => $this->id
			]);
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.Update (1)', 0, $ex);
		}
		try {
			switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
				case 'mysql':
					$sql = <<<TXT
insert into users_profile (usersid, fio, telephonenumber, homephone, jpegphoto, post)
values (:usersid, :fio, :telephonenumber, :homephone, :jpegphoto, :post)
on duplicate key update
	fio = :fio,
	telephonenumber = :telephonenumber,
	homephone = :homephone,
	jpegphoto = :jpegphoto,
	post = :post
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
insert into users_profile (usersid, fio, telephonenumber, homephone, jpegphoto, post)
values (:usersid, :fio, :telephonenumber, :homephone, :jpegphoto, :post)
on conflict(usersid) do update set
	fio = :fio,
	telephonenumber = :telephonenumber,
	homephone = :homephone,
	jpegphoto = :jpegphoto,
	post = :post
TXT;
					break;
			}

			DB::prepare($sql)->execute([
				':usersid' => $this->id,
				':fio' => $this->fio,
				':telephonenumber' => $this->telephonenumber,
				':homephone' => $this->homephone,
				':jpegphoto' => $this->jpegphoto,
				':post' => $this->post
			]);
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.Update (2)', 0, $ex);
		}
	}

	/**
	 * Получает данные о пользователе из базы
	 * @param string $where
	 * @param array $params
	 * @return boolean
	 */
	function select($where, $params) {
		try {
			$sql = <<<TXT
select
	p.*,
	u.*,
	u.id sid
from users u
	left join users_profile p on p.usersid = u.id
where $where
TXT;
			$row = DB::prepare($sql)->execute($params)->fetch();
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
				$this->post = $row['post'];
				return true;
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка при получении данных пользователя', 0, $ex);
		}
		return false;
	}

	/**
	 * Получить данные о пользователе по логину
	 * @param string $login
	 * @return boolean
	 */
	function getByLogin($login) {
		return $this->select('u.login = :login', [':login' => $login]);
	}

	/**
	 * Получить данные о пользователе по идентификатору
	 * @param integer $id
	 * @return boolean
	 */
	function getById($id) {
		return $this->select('u.id = :id', [':id' => $id]);
	}

	/**
	 * Получить данные о пользователе по идентификатору randomid
	 * @param string $randomid
	 * @return boolean
	 */
	function getByRandomId($randomid) {
		return $this->select('u.randomid = :randomid', [':randomid' => $randomid]);
	}

	/**
	 * Получить данные о пользователе по идентификатору. БЕЗ ПРОФИЛЯ
	 * @param type $randomid
	 * @return boolean
	 */
	function getByRandomIdNoProfile($randomid) {
		$sql = 'select * from users where randomid = :randomid';
		try {
			$row = DB::prepare($sql)->execute([':randomid' => $randomid])->fetch();
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
			throw new DBException('Ошибка выполнения User.getByRandomIdNoProfile', 0, $ex);
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
insert into users (randomid, orgid, login, password, salt, email, mode, lastdt, active)
values (:randomid, :orgid, :login, :password, :salt, :email, :mode, now(), 1)
TXT;
		try {
			DB::prepare($sql)->execute([
				':randomid' => $this->randomid,
				':orgid' => $this->orgid,
				':login' => $this->login,
				':password' => $this->password,
				':salt' => $this->salt,
				':email' => $this->email,
				':mode' => $this->mode
			]);
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения User.Add', 0, $ex);
		}

		$zx = new BaseUser();

		if ($zx->getByRandomIdNoProfile($this->randomid)) {
			// добавляю профиль
			$sql = <<<TXT
insert into users_profile (usersid, fio, telephonenumber, homephone, jpegphoto, post)
values (:userid, :fio, :telephonenumber, :homephone, :jpegphoto, :post)
TXT;
			try {
				DB::prepare($sql)->execute([
					':userid' => $zx->id,
					':fio' => $this->fio,
					':telephonenumber' => $this->telephonenumber,
					':homephone' => $this->homephone,
					':jpegphoto' => $this->jpegphoto,
					':post' => $this->post
				]);
			} catch (PDOException $ex) {
				throw new DBException('Ошибка выполнения User.Add', 0, $ex);
			}
		} else {
			die('Не найден пользователь по randomid User.Add');
		}
		unset($zx);
	}

	function isAdmin() {
		return ($this->mode == 1);
	}

}
