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

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
// Запускаем установщик при условии, что файл настроек отсутствует.
if (file_exists(SITE_ROOT . '/app/config.php')) {
	die('Система уже установлена.<br>Если желаете переустановить, то удалите файл /app/config.php');
}

require_once SITE_ROOT . '/inc/functions.php';

$dbDriver = filter_input(INPUT_POST, 'dbdriver', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(mysql|pgsql)$/']]);
if ($dbDriver === false) {
	die('Ошибка: неверный параметр dbdriver');
}
$dbHost = PostDef('dbhost');
$dbName = PostDef('dbname');
$dbUser = PostDef('dbuser');
$dbPass = PostDef('dbpass');
$orgName = PostDef('orgname');
$login = PostDef('login');
$pass = PostDef('pass');

// Загружаем скрипт создания таблиц.
$text = file_get_contents(SITE_ROOT . "/install/$dbDriver.scheme.sql");
if (!$text) {
	die("Ошибка открытия файла /install/$dbDriver.scheme.sql");
}

$opt = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES => true
];

try {
	$dbh = new PDO("$dbDriver:host=$dbHost", $dbUser, $dbPass, $opt);

	// Создаём БД
	if (/* $dbh->getAttribute(PDO::ATTR_DRIVER_NAME) */$dbDriver == 'mysql') {
		$dbh->exec("create database if not exists $dbName") or die('Ошибка создания базы: ' . implode('<br>', $dbh->errorInfo()));
	} else {
		$sql = "select count(*) cnt from pg_catalog.pg_database where datname = :name";
		$sth = $dbh->prepare($sql);
		$sth->execute([':name' => $dbName]);
		$row = $sth->fetch();
		if ($row) {
			if ($row['cnt'] == 0) {
				die("База данных '$dbName' не найдена");
			}
		}
		// Подключаем необходимое расширение "pgcrypto"
		$sql = "select count(*) cnt from pg_available_extensions where name='pgcrypto' and installed_version is not null";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$row = $sth->fetch();
		if ($row) {
			if ($row['cnt'] == 0) {
//				$dbh->exec('CREATE EXTENSION pgcrypto');
			}
		}
//		$dbh->exec("DROP DATABASE IF EXISTS $dbName");
//		$dbh->exec("CREATE DATABASE $dbName");
	}

	$dbh = null;

	// Переподключаемся к СУБД
	$dbh = new PDO("$dbDriver:host=$dbHost;dbname=$dbName", $dbUser, $dbPass, $opt);
} catch (PDOException $ex) {
	die('Ошибка БД: ' . $ex->getMessage());
}

try {
	$dbh->beginTransaction();

//	$imported = $dbh->exec($text);
//	if ($imported === false) {
//		die(implode('<br>', $dbh->errorInfo()));
//	}
	$dbh->exec($text);

	// Создаём настройки в БД
	if ($dbDriver == 'mysql') {
		$sql = <<<SQL1
insert into config (ad, theme, sitename, smtpauth, sendemail, version) values (0, 'cerulean', 'Inventory - Учёт оргтехники', 0, 0, :version)
SQL1;
	} else {
		$sql = <<<SQL2
insert into public.config (ad, theme, sitename, smtpauth, sendemail, version) VALUES (0, 'cerulean', 'Inventory - Учёт оргтехники', 0, 0, :version)
SQL2;
	}
	$sth = $dbh->prepare($sql);
	$sth->execute([':version' => SITE_VERSION]);

	// Создаём организацию
	if ($dbDriver == 'mysql') {
		$sql = 'insert into org (name, active) values (:orgname, 1)';
	} else {
		$sql = 'insert into public.org (name, active) values (:orgname, 1)';
	}
	$sth = $dbh->prepare($sql);
	$sth->execute([':orgname' => $orgName]);

	// Создаём администратора
	$salt = generateSalt();
	$password = sha1(sha1($pass) . $salt);
	if ($dbDriver == 'mysql') {
		$sql = <<<SQL3
insert into users (randomid, orgid, login, password, salt, email, mode, lastdt, active)
values (:randomid, 1, :login, :password, :salt, 'admin@localhost', 1, now(), 1)
on duplicate key update
	randomid = :randomid,
	password = :password,
	salt = :salt
SQL3;
	} else {
		$sql = <<<SQL4
insert into public.users (randomid, orgid, login, password, salt, email, mode, lastdt, active)
values (:randomid, 1, :login, :password, :salt, 'admin@localhost', 1, now(), 1)
SQL4;
	}
	$sth = $dbh->prepare($sql);
	$sth->execute([':randomid' => getRandomId(), ':login' => $login, ':password' => $password, ':salt' => $salt]);

	// Создаём профиль администратора
	if ($dbDriver == 'mysql') {
		$sql = <<<SQL5
insert into users_profile (usersid, fio, post, telephonenumber, homephone, jpegphoto)
values (1, 'Администратор', 'Системный администратор', '', '2000', '')
SQL5;
	} else {
		$sql = <<<SQL6
insert into public.users_profile (usersid, fio, post, telephonenumber, homephone, jpegphoto)
values (1, 'Администратор', 'Системный администратор', '', '2000', '')
SQL6;
	}
	$dbh->exec($sql);

	$dbh->commit();
} catch (PDOException $ex) {
	$dbh->rollBack();
	die('Ошибка БД: ' . $ex->getMessage());
}

$data = '<?php
$debug = false; // Режим отладки
$db_driver = "' . $dbDriver . '";
$db_char = "utf8"; // Кодировка базы
$db_host = "' . $dbHost . '"; // Хост БД
$db_user = "' . $dbUser . '"; // Пользователь БД
$db_pass = "' . $dbPass . '"; // Пароль пользователя БД
$db_base = "' . $dbName . '"; // Имя базы
$rewrite_base = "/";
';

$file = SITE_ROOT . '/app/config.php';
file_put_contents($file, $data, LOCK_EX) or die('Ошибка записи в файл: /app/config.php');
chmod($file, 0664);

exit('ok');
