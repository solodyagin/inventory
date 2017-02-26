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

// Запускаем установщик при условии, что файл настроек отсутствует
if (file_exists(WUO_ROOT . '/config.php')) {
	die('Система уже установлена.<br>Если желаете переустановить, то удалите файл config.php');
}

include_once(WUO_ROOT . '/inc/functions.php');

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

	$opt = array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => true
	);
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, $opt);

	$text = file_get_contents(WUO_ROOT . '/install/structure.sql');
	if (!$text) {
		die('<div class="alert alert-danger">Ошибка открытия файла: structure.sql</div>');
	}

	$imported = $dbh->exec($text);
	if ($imported === false) {
		die(print_r($dbh->errorInfo(), true));
	}

	// Создаём настройки
	$sql = <<<SQL
INSERT INTO `config` (`ad`, `theme`, `sitename`, `smtpauth`, `sendemail`, `version`)
VALUES (0, 'bootstrap', 'Учёт оргтехники', 0, 0, '3.74')
SQL;
	$dbh->exec($sql);

	// Создаём организацию
	$dbh->prepare('INSERT INTO org (name, active) VALUES (:orgname, 1)')->execute(array(':orgname' => $orgname));

	// Создаём пользователя
	$salt = generateSalt();
	$password = sha1(sha1($pass) . $salt);
	$sql = <<<SQL
INSERT INTO users (`randomid`, `orgid`, `login`, `password`, `salt`, `mode`, `lastdt`, `active`)
VALUES (:randomid, 1, :login, :password, :salt, 1, NOW(), 1)
ON DUPLICATE KEY UPDATE
	`randomid` = :randomid,
	`password` = :password,
	`salt` = :salt
SQL;
	$dbh->prepare($sql)->execute(array(
		':randomid' => GetRandomId(),
		':login' => $login,
		':password' => $password,
		':salt' => $salt
	));
} catch (PDOException $ex) {
	die('<div class="alert alert-danger">Ошибка БД: ' . $ex->getMessage() . '</div>');
}

$data = '<?php
$debug = false; // Режим отладки - РЕКОМЕНДУЮ поставить false !!!
$userewrite = 0; // Использовать модуль mod_rewrite для ЧПУ
$mysql_char = "utf8"; // Кодировка базы
$mysql_host = "' . $dbhost . '"; // Хост БД
$mysql_user = "' . $dbuser . '"; // Пользователь БД
$mysql_pass = "' . $dbpass . '"; // Пароль пользователя БД
$mysql_base = "' . $dbname . '"; // Имя базы
// Если активен режим отладки, то показываем все ошибки и предупреждения
if ($debug) {
	ini_set("display_errors", 1);
	error_reporting(E_ALL);
}';

$file = WUO_ROOT . '/config.php';
file_put_contents($file, $data, LOCK_EX) or die('<div class="alert alert-danger">Ошибка записи в файл: config.php</div>');

echo 'ok';
