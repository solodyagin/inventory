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

// Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

use core\baseuser;
use core\request;
use core\user;
use core\utils;
use core\db;
use core\dbexception;

$err = [];

// Требуются полные права!
$user = user::getInstance();
if ($user->isAdmin() || $user->testRights[1]) {
	// Получаем переменные, проверяем на правильность заполнения
	$req = request::getInstance();
	$step = $req->get('step');
	$orgid = $req->get('orgid');
	if ($orgid == '') {
		$err[] = 'Не выбрана организация!';
	}
	$login = $req->get('login');
	if ($login == '') {
		$err[] = 'Не задан логин!';
	}
	$pass = $req->get('pass');
	$email = $req->get('email');
	if ($email == '') {
		$err[] = 'Не задан E-mail!';
	}
	$mode = $req->get('mode');
	if ($mode == '') {
		$err[] = 'Не задан режим!';
	}
	if (!preg_match('/^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.[a-zA-Z]{2,4}$/', $email)) {
		$err[] = 'Не верно указан E-mail';
	}

	// Добавляем пользователя
	if ($step == 'add') {
		if ($pass == '') { // пароль не может быть пустым при добавлении пользователя
			$err[] = 'Не задан пароль!';
		}
		if (utils::doubleLogin($login) != 0) {
			$err[] = 'Такой логин уже есть в базе!';
		}
		if (utils::doubleEmail($email) != 0) {
			$err[] = 'Такой E-mail уже есть в базе!';
		}
		if (count($err) == 0) {
			$tmpuser = new baseuser();
			$tmpuser->active = 1;
			$tmpuser->fio = $login;
			$tmpuser->post = '';
			$tmpuser->telephonenumber = '';
			$tmpuser->homephone = '';
			$tmpuser->jpegphoto = '';
			$tmpuser->add(utils::getRandomId(60), $orgid, $login, $pass, $email, $mode);
		}
	}

	// Редактируем пользователя
	if ($step == 'edit') {
		if (count($err) == 0) {
			$id = $req->get('id');
			$ps = ($pass != '') ? " password=sha1(concat(sha1('$pass'), salt))," : '';
			$sql = <<<TXT
update users
set orgid = :orgid, login = :login, $ps email = :email, mode = :mode
where id = :id
TXT;
			try {
				db::prepare($sql)->execute([
					':orgid' => $orgid,
					':login' => $login,
					':email' => $email,
					':mode' => $mode,
					':id' => $id
				]);
			} catch (PDOException $ex) {
				throw new dbexception('Не могу обновить данные по пользователю', 0, $ex);
			}
		}
	}
} else {
	$err[] = 'Для выполнения текущей операции требуются полные права';
}

if (count($err) == 0) {
	echo 'ok';
} else {
	echo '<script>$("#messenger").addClass("alert alert-danger");</script>';
	echo implode('<br>', $err);
}
