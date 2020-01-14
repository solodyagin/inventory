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

/* Запускаем установщик при условии, что файл настроек отсутствует */
if (file_exists(SITE_ROOT . '/app/config.php')) {
	die('Система уже установлена.<br>Если желаете переустановить, то удалите файл /app/config.php');
}

include_once(SITE_ROOT . '/inc/functions.php');

$dbhost = PostDef('dbhost');
$dbname = PostDef('dbname');
$dbuser = PostDef('dbuser');
$dbpass = PostDef('dbpass');
$orgname = PostDef('orgname');
$login = PostDef('login');
$pass = PostDef('pass');

try {
	$dbh = new PDO("mysql:host=$dbhost", $dbuser, $dbpass);
	$dbh->exec("CREATE DATABASE IF NOT EXISTS $dbname")
			or die('<div class="alert alert-danger">Ошибка создания базы: ' . $dbh->errorInfo() . '</div>');

	$opt = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => true
	];
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, $opt);

	$text = file_get_contents(SITE_ROOT . '/install/mysql.scheme.sql');
	if (!$text) {
		die('<div class="alert alert-danger">Ошибка открытия файла /install/mysql.scheme.sql</div>');
	}

	$imported = $dbh->exec($text);
	if ($imported === false) {
		die(print_r($dbh->errorInfo(), true));
	}

	/* Создаём настройки в БД */
	$sql = <<<SQL
INSERT INTO `config` (`ad`, `theme`, `sitename`, `smtpauth`, `sendemail`, `version`)
VALUES (0, 'bootstrap', 'Inventory - Учёт оргтехники', 0, 0, :version)
SQL;
	$dbh->prepare($sql)->execute([
		':version' => SITE_VERSION
	]);

	/* Создаём организацию */
	$dbh->prepare('INSERT INTO org (name, active) VALUES (:orgname, 1)')->execute([':orgname' => $orgname]);

	/* Создаём администратора */
	$salt = generateSalt();
	$password = sha1(sha1($pass) . $salt);
	$sql = <<<SQL
INSERT INTO users (`randomid`, `orgid`, `login`, `password`, `salt`, `email`, `mode`, `lastdt`, `active`)
VALUES (:randomid, 1, :login, :password, :salt, 'admin@localhost', 1, NOW(), 1)
ON DUPLICATE KEY UPDATE
	`randomid` = :randomid,
	`password` = :password,
	`salt` = :salt
SQL;
	$dbh->prepare($sql)->execute([
		':randomid' => GetRandomId(),
		':login' => $login,
		':password' => $password,
		':salt' => $salt
	]);

	/* Создаём профиль администратора */
	$sql = <<<SQL
INSERT INTO users_profile (usersid, fio, post, telephonenumber, homephone, jpegphoto)
VALUES (1, 'Администратор', 'sysadmin', '', '2000', '')
SQL;
	$dbh->exec($sql);

} catch (PDOException $ex) {
	die('<div class="alert alert-danger">Ошибка БД: ' . $ex->getMessage() . '</div>');
}

$data = '<?php
$debug = false; // Режим отладки
$mysql_char = "utf8"; // Кодировка базы
$mysql_host = "' . $dbhost . '"; // Хост БД
$mysql_user = "' . $dbuser . '"; // Пользователь БД
$mysql_pass = "' . $dbpass . '"; // Пароль пользователя БД
$mysql_base = "' . $dbname . '"; // Имя базы
$rewrite_base = "/";
';

$file = SITE_ROOT . '/app/config.php';
file_put_contents($file, $data, LOCK_EX) or die('<div class="alert alert-danger">Ошибка записи в файл: /app/config.php</div>');

echo 'ok';
