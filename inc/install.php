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

include_once(WUO_ROOT . '/inc/functions.php'); // Класс работы с БД

$dbhost = PostDef('dbhost');
$dbname = PostDef('dbname');
$dbuser = PostDef('dbuser');
$dbpass = PostDef('dbpass');
$orgname = PostDef('orgname');
$login = PostDef('login');
$pass = PostDef('pass');

$conn = @mysqli_connect($dbhost, $dbuser, $dbpass);
if (!$conn) {
	die('<div class="alert alert-danger">Ошибка БД: ' . mysqli_connect_error() . '</div>');
}

$res = mysqli_select_db($conn, $dbname);
if (!$res) {
	$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
	if (!mysqli_query($conn, $sql)) {
		die('<div class="alert alert-danger">Ошибка создания базы: ' . mysqli_error($conn) . '</div>');
	}
}

mysqli_select_db($conn, $dbname);

$handle = file_get_contents(WUO_ROOT . '/webuser.sql');
if (!$handle) {
	die('<div class="alert alert-danger">Ошибка открытия файла: webuser.sql</div>');
}

if (mysqli_multi_query($conn, $handle)) {
	do {
		if ($result = mysqli_store_result($conn)) {
			mysqli_free_result($result);
		}
		if (mysqli_more_results($conn)) {
			
		}
	} while (mysqli_next_result($conn));
}

// ну и теперь меняю название организации и логин/пароль пользователя
$orgname = mysqli_real_escape_string($conn, $orgname);
$sql = "UPDATE org SET name = '$orgname';";
$result = mysqli_query($conn, $sql);
if (!$result) {
	die('<div class="alert alert-danger">Ошибка БД (1): ' . mysqli_error($conn) . '</div>');
}

$salt = generateSalt();
$password = sha1(sha1($pass) . $salt);
$sql = "UPDATE users SET password = '$password', salt = '$salt', login = '$login' WHERE id = 1;";
$result = mysqli_query($conn, $sql);
if (!$result) {
	die('<div class="alert alert-danger">Ошибка БД (2): ' . mysqli_error($conn) . '</div>');
}

//чета не доверяю я проверке на ошибки.. Проверю ка руками!
$sql = 'SELECT * FROM config';
$result = mysqli_query($conn, $sql);
if (!$result) {
	die('<div class="alert alert-danger">Что-то пошло не так (с). Попробуйте залить дамп webuser.sql в базу в ручном режиме. Затем переименуйте файл config.dist в config.php и отредактируйте!</div>');
}

mysqli_close($conn);

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
