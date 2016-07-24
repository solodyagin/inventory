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

$host = PostDef('host');
$basename = PostDef('basename');
$baseusername = PostDef('baseusername');
$passbase = PostDef('passbase');
$orgname = PostDef('orgname');
$login = PostDef('login');
$pass = PostDef('pass');

$idsqlconnection = @new mysqli($host, $baseusername, $passbase, $basename);
if (mysqli_connect_errno()) {
	$serr = mysqli_connect_error();
	die('<div class="alert alert-danger">Ошибка БД: ' . $serr . '</div>');
}
$handle = file_get_contents(WUO_ROOT . '/webuser.sql', 'r');
if (!$handle) {
	die('<div class="alert alert-danger">Ошибка открытия файла: webuser.sql</div>');
}
if (mysqli_multi_query($idsqlconnection, $handle)) {
	do {
		if ($result = mysqli_store_result($idsqlconnection)) {
			mysqli_free_result($result);
		}
		if (mysqli_more_results($idsqlconnection)) {

		}
	} while (mysqli_next_result($idsqlconnection));
}

//ну и теперь меняю название организации и логин/пароль пользователя
$orgname = mysqli_real_escape_string($idsqlconnection, $orgname);
$sql = "UPDATE org SET name = '$orgname';";
$result = mysqli_query($idsqlconnection, $sql);
if ($result == '') {
	$serr = mysqli_error($idsqlconnection);
	die('<div class="alert alert-danger">Ошибка БД (1): ' . $serr . '</div>');
}
$salt = generateSalt();
$password = sha1(sha1($pass) . $salt);
$sql = "UPDATE users SET password = '$password', salt = '$salt', login = '$login' WHERE id = 1;";
$result = mysqli_query($idsqlconnection, $sql);
if ($result == '') {
	$serr = mysqli_error($idsqlconnection);
	die('<div class="alert alert-danger">Ошибка БД (2): ' . $serr . '</div>');
}

//чета не доверяю я проверке на ошибки.. Проверю ка руками!
$sql = 'SELECT * FROM config';
$result = mysqli_query($idsqlconnection, $sql);
if (!$result) {
	die('<div class="alert alert-danger">Что-то пошло не так (с). Попробуйте залить дамп webuser.sql в базу в ручном режиме. Затем переименуйте файл config.dist в config.php и отредактируйте!</div>');
}
echo 'ok';
