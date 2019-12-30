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

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

$err = array();

// Требуются полные права!
if (($user->mode == 1) || $user->TestRoles('1')) {
	// Получаем переменные, проверяем на правильность заполнения
	$step = GetDef('step');
	$orgid = PostDef('orgid');
	if ($orgid == '') {
		$err[] = 'Не выбрана организация!';
	}
	$login = PostDef('login');
	if ($login == '') {
		$err[] = 'Не задан логин!';
	}
	$pass = PostDef('pass');
	$email = PostDef('email');
	if ($email == '') {
		$err[] = 'Не задан E-mail!';
	}
	$mode = PostDef('mode');
	if ($mode == '') {
		$err[] = 'Не задан режим!';
	}
	if (!preg_match('/^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.[a-zA-Z]{2,4}$/', $email)) {
		$err[] = 'Не верно указан E-mail';
	}

	if ($step == 'add') {
		if ($pass == '') { // пароль не может быть пустым при добавлении пользователя
			$err[] = 'Не задан пароль!';
		}
		if (DoubleLogin($login) != 0) {
			$err[] = 'Такой логин уже есть в базе!';
		}
		if (DoubleEmail($email) != 0) {
			$err[] = 'Такой E-mail уже есть в базе!';
		}
	}
	/* Закончили всяческие проверки */

	// Добавляем пользователя
	if ($step == 'add') {
		if (count($err) == 0) {
			$tmpuser = new BaseUser();
			$tmpuser->active = 1;
			$tmpuser->fio = $login;
			$tmpuser->post = '';
			$tmpuser->telephonenumber = '';
			$tmpuser->homephone = '';
			$tmpuser->jpegphoto = '';
			$tmpuser->Add(GetRandomId(60), $orgid, $login, $pass, $email, $mode);
		}
	}

	// Редактируем пользователя
	if ($step == 'edit') {
		if (count($err) == 0) {
			$id = GetDef('id');
			$ps = ($pass != '') ? " password=SHA1(CONCAT(SHA1('$pass'), salt))," : '';
			$sql = <<<TXT
UPDATE users
SET orgid = :orgid, login = :login, $ps email = :email, mode = :mode
WHERE id = :id
TXT;
			try {
				DB::prepare($sql)->execute(array(
					':orgid' => $orgid,
					':login' => $login,
					':email' => $email,
					':mode' => $mode,
					':id' => $id
				));
			} catch (PDOException $ex) {
				throw new DBException('Не могу обновить данные по пользователю', 0, $ex);
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
	for ($i = 0; $i < count($err); $i++) {
		echo "$err[$i]<br>";
	}
}
