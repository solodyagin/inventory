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

/**
 * Массив переданных скрипту параметров при загрузке его через index.php
 * Например, index.php?route=/script.php?par1=value&par2=value2
 * $PARAMS['par1'] = 'value'
 * $PARAMS['par2'] = 'value2'
 */
$PARAMS = [];

/**
 * Возвращает значение $_GET[$name] или $def
 * @param string $name
 * @param string $def
 * @return string
 */
function GetDef($name, $def = '') {
	global $PARAMS;
	if (isset($_GET[$name])) {
		return $_GET[$name];
	}
	if (isset($PARAMS[$name])) {
		return $PARAMS[$name];
	}
	return $def;
}

/**
 * Возвращает значение $_POST[$name] или $def
 * @param string $name
 * @param string $def
 * @return string
 */
function PostDef($name, $def = '') {
	return (isset($_POST[$name])) ? $_POST[$name] : $def;
}

/**
 * Возвращает массив из папок в указанной папке
 * @param string $dir
 * @return array
 */
function GetArrayFilesInDir($dir) {
	$includes_dir = opendir("$dir");
	$files = [];
	while (($inc_file = readdir($includes_dir)) != false) {
		if (($inc_file != '.') and ( $inc_file != '..')) {
			$files[] = $inc_file;
		}
	}
	closedir($includes_dir);
	sort($files);
	return $files;
}

/**
 * Проверяет аутентификацию в AD
 * @param string $username
 * @param string $password
 * @param string $ladpserver
 * @param string $domain1
 * @param string $domain2
 * @return string Результат true если в AD такой пользователь есть
 */
function check_LDAP_user($username, $password, $ladpserver, $domain1, $domain2) {
	$res = false;
	if ($password && $username) {
		//$filter = "(&(objectClass=top)(sAMAccountName=" . $username . "))";
		//$basedn = "dc=$domain1,dc=$domain2";
		$dn = "$domain1\\$username";
		$ldapconn = ldap_connect($ladpserver);
		if ($ldapconn) {
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
			if (@ldap_bind($ldapconn, $dn, $password)) {
				$res = true;
				@ldap_unbind($ldapconn);
			} else {
				$res = false;
			}
		}
	}
	return $res;
}

/**
 * Получает случайный идентификатор длиной $n
 * @param integer $n
 * @return string
 */
function getRandomId($n = 60) {
	$arr = ['a', 'b', 'c', 'd', 'e', 'f',
		'g', 'h', 'i', 'j', 'k', 'l',
		'm', 'n', 'o', 'p', 'r', 's',
		't', 'u', 'v', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F',
		'G', 'H', 'I', 'J', 'K', 'L',
		'M', 'N', 'O', 'P', 'R', 'S',
		'T', 'U', 'V', 'X', 'Y', 'Z',
		'1', '2', '3', '4', '5', '6',
		'7', '8', '9', '0'];
	$res = '';
	for ($i = 0; $i < $n; $i++) {
		// Вычисляем случайный индекс массива
		$index = rand(0, count($arr) - 1);
		$res .= $arr[$index];
	}
	return $res;
}

/**
 * Получает строку со случайными цифрами длиной $n
 * @param integer $n
 * @return string
 */
function getRandomDigits($n = 60) {
	$res = '';
	for ($i = 0; $i < $n; $i++) {
		$res .= chr(rand(48, 56));
	}
	return $res;
}

/**
 * Преобразует дату типа dd.mm.2012 в формат MySQL 2012-01-01 00:00:00
 * @param type $dt
 * @return string
 */
function DateToMySQLDateTime2($dt) {
	$str_exp = explode('.', $dt);
	if ((strpos($str_exp[2], ' ') === false)) {
		$dtt = $str_exp[2] . '-' . $str_exp[1] . '-' . $str_exp[0];
	} else {
		$st2 = explode(' ', $str_exp[2]);
		$yy = trim($st2[0]);
		$dtt = $yy . '-' . $str_exp[1] . '-' . $str_exp[0];
	}
	return $dtt;
}

/**
 * Преобразует дату MySQL 2012-01-01 00:00:00 в dd.mm.2012 00:00:00
 * @param type $dt
 * @return string
 */
function MySQLDateTimeToDateTime($dt) {
	$str1 = explode('-', $dt);
	$str2 = explode(' ', $str1[2]);
	$dtt = $str2[0] . '.' . $str1[1] . '.' . $str1[0] . ' ' . $str2[1];
	return $dtt;
}

/**
 * Преобразует дату MySQL 2012-01-01 00:00:00 в dd.mm.2012 00:00:00
 * @param type $dt
 * @return string
 */
function MySQLDateToDate($dt) {
	$str1 = explode('-', $dt);
	$dtt = $str1[2] . '.' . $str1[1] . '.' . $str1[0];
	return $dtt;
}

/**
 * Преобразует дату MySQL 2012-01-01 00:00:00 в dd.mm.2012
 * @param type $dt
 * @return string
 */
function MySQLDateTimeToDateTimeNoTime($dt) {
	$str1 = explode('-', $dt);
	$str2 = explode(' ', $str1[2]);
	$dtt = $str2[0] . '.' . $str1[1] . '.' . $str1[0];
	return $dtt;
}

/**
 * Получает последнюю "закрепленную" новость
 * @return type
 * @throws DBException
 */
function GetStiker() {
	$stik['body'] = '';
	$stik['title'] = '';
	$sql = 'SELECT * FROM news WHERE stiker = 1 ORDER BY id LIMIT 1';
	try {
		$row = DB::prepare($sql)->execute()->fetch();
		if ($row) {
			$stik['body'] = $row['body'];
			$stik['title'] = $row['title'];
		}
	} catch (PDOException $ex) {
		throw new DBException('Неверный запрос GetStiker', 0, $ex);
	}
	return $stik;
}

/**
 * Возвращает массив активных организаций
 * @return array
 * @throws Exception
 */
function GetArrayOrgs() {
	$mOrgs = [];
	$sql = 'SELECT * FROM org WHERE active = 1 ORDER BY name';
	try {
		$cnt = 0;
		$arr = DB::prepare($sql)->execute()->fetchAll();
		foreach ($arr as $row) {
			$mOrgs[$cnt]['id'] = $row['id'];
			$mOrgs[$cnt]['name'] = $row['name'];
			$mOrgs[$cnt]['picnmap'] = $row['picmap'];
			$mOrgs[$cnt]['active'] = $row['active'];
			$cnt++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Неверный запрос GetArrayOrgs', 0, $ex);
	}
	return $mOrgs;
}

/**
 * Возвращает массив активных организаций
 * @return type
 * @throws DBException
 */
function GetArrayKnt() {
	$mOrgs = [];
	$sql = 'SELECT * FROM knt WHERE active = 1 ORDER BY name';
	try {
		$cnt = 0;
		$arr = DB::prepare($sql)->execute()->fetchAll();
		foreach ($arr as $row) {
			$mOrgs[$cnt]['id'] = $row['id'];
			$mOrgs[$cnt]['name'] = $row['name'];
			$mOrgs[$cnt]['active'] = $row['active'];
			$cnt++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Неверный запрос GetArrayKnt', 0, $ex);
	}
	return $mOrgs;
}

function mailq($to, $subject, $content, $attach = false) {
	$cfg = Config::getInstance();
	sendMailAttachment($to, $cfg->smtpusername, $subject, $content);
}

function sendMailAttachment($mailTo, $from, $subject, $message, $file = false) {
	$ffn = basename($file);
	$separator = "---"; // разделитель в письме
	// Заголовки для письма
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "From: $from\nReply-To: $from\r\n"; // задаем от кого письмо
	//  $headers .= "Content-Type: text/html; charset=utf-8\r\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"$separator\""; // в заголовке указываем разделитель
	// если письмо с вложением
	if ($file) {
		$bodyMail = "--$separator\n"; // начало тела письма, выводим разделитель
		$bodyMail .= "Content-type: text/html; charset='utf-8'\n"; // кодировка письма
		$bodyMail .= "Content-Transfer-Encoding: quoted-printable"; // задаем конвертацию письма
		$bodyMail .= "Content-Disposition: attachment; filename=?utf-8?B?" . base64_encode(basename($file)) . "?=\n\n"; // задаем название файла
		$bodyMail .= $message . "\n"; // добавляем текст письма
		$bodyMail .= "--$separator\n";
		$fileRead = fopen($file, "r"); // открываем файл
		$contentFile = fread($fileRead, filesize($file)); // считываем его до конца
		fclose($fileRead); // закрываем файл
		$bodyMail .= "Content-Type: application/octet-stream; name=\"$ffn\"\n";
		$bodyMail .= "Content-Transfer-Encoding: base64\n"; // кодировка файла
		$bodyMail .= "Content-Disposition: attachment; filename=\"$ffn\"\n\n";
		$bodyMail .= chunk_split(base64_encode($contentFile)) . "\n"; // кодируем и прикрепляем файл
		$bodyMail .= "--" . $separator . "--\n";
		// письмо без вложения
	} else {
		$bodyMail = "--$separator\n"; // начало тела письма, выводим разделитель
		$bodyMail .= "Content-type: text/html; charset='utf-8'\n"; // кодировка письма
		$bodyMail = $bodyMail . $message . "\n";
	}
	$result = mail($mailTo, $subject, $bodyMail, $headers); // отправка письма
	return $result;
}

/**
 * Заносит письмо в очередь для отправки
 * @param string $to
 * @param string $subject
 * @param string $content
 * @param boolean $attach
 * @throws DBException
 */
function smtpmail($to, $subject, $content, $attach = false) {
	$sql = "INSERT INTO mailq (id, `from`, `to`, `title`, btxt) VALUES (null, '', :to, :subject, :content)";
	try {
		DB::prepare($sql)->execute([
			':to' => $to,
			':subject' => $subject,
			':content' => $content
		]);
	} catch (PDOException $ex) {
		throw new DBException('Не удалось записать очередь сообщений', 0, $ex);
	}
}

/**
 * Проверяет есть ли дубли логинов в базе. Результат - количество логинов
 * @param string $login
 * @return integer
 * @throws DBException
 */
function DoubleLogin($login) {
	$cnt = 0;
	$sql = 'SELECT COUNT(id) as cnt FROM users WHERE login = :login';
	try {
		$row = DB::prepare($sql)->execute([':login' => $login])->fetch();
		if ($row) {
			$cnt = $row['cnt'];
		}
	} catch (PDOException $ex) {
		throw new DBException('Неверный запрос DoubleLogin', 0, $ex);
	}
	return $cnt;
}

/**
 * Проверяет есть ли дубли логинов в базе. Результат - количество логинов
 * @param string $email
 * @return integer
 * @throws DBException
 */
function DoubleEmail($email) {
	$cnt = 0;
	$sql = 'SELECT COUNT(id) as cnt FROM users WHERE email = :email';
	try {
		$row = DB::prepare($sql)->execute([':email' => $email])->fetch();
		if ($row) {
			$cnt = $row['cnt'];
		}
	} catch (PDOException $ex) {
		throw new DBException('Неверный запрос DoubleEmail', 0, $ex);
	}
	return $cnt;
}

function ReUpdateRepairEq() {
	try {
		// листаем весь список ТМЦ
		$arr = DB::prepare('SELECT * FROM equipment')->execute()->fetchAll();
		foreach ($arr as $row) {
			$uid = $row['id'];
			$rs = 0;
			// Для каждого ТМЦ проверяем "что у нас с ремонтами"
			$row2 = DB::prepare('SELECT * FROM repair WHERE eqid = :uid ORDER BY id DESC LIMIT 1')->execute([':uid' => $uid])->fetch();
			if ($row2) {
				$rs = $row2['status'];
			}
			DB::prepare('UPDATE equipment SET repair = :rs WHERE id= :uid')->execute(array(':rs' => $rs, ':uid' => $uid));
		}
	} catch (PDOException $ex) {
		throw new DBException('Неверный запрос ReUpdateRepairEq', 0, $ex);
	}
}

/**
 * Генерирует пароль
 * @param type $number
 * @return string
 */
function generatePassword($number) {
	$arr = ['a', 'b', 'c', 'd', 'e', 'f',
		'g', 'h', 'i', 'j', 'k', 'l',
		'm', 'n', 'o', 'p', 'r', 's',
		't', 'u', 'v', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F',
		'G', 'H', 'I', 'J', 'K', 'L',
		'M', 'N', 'O', 'P', 'R', 'S',
		'T', 'U', 'V', 'X', 'Y', 'Z',
		'1', '2', '3', '4', '5', '6',
		'7', '8', '9', '0'];
	$pass = '';
	for ($i = 0; $i < $number; $i++) {
		// Вычисляем случайный индекс массива
		$index = rand(0, count($arr) - 1);
		$pass .= $arr[$index];
	}
	return $pass;
}

function generateEAN($number) {
	$code = '480' . str_pad($number, 9, '0');
	$weightflag = true;
	$sum = 0;
	// Weight for a digit in the checksum is 3, 1, 3.. starting from the last digit.
	// loop backwards to make the loop length-agnostic. The same basic functionality
	// will work for codes of different lengths.
	for ($i = strlen($code) - 1; $i >= 0; $i--) {
		$sum += (int) $code[$i] * ($weightflag ? 3 : 1);
		$weightflag = !$weightflag;
	}
	$code .= (10 - ($sum % 10)) % 10;
	return $code;
}

function generateSalt() {
	$salt = '';
	$length = rand(5, 10); // длина соли (от 5 до 10 сомволов)
	for ($i = 0; $i < $length; $i++) {
		$salt .= chr(rand(33, 126)); // символ из ASCII-table
	}
	return $salt;
}

function jsonExit($data) {
	header('Content-type: application/json; charset=utf-8');
	echo json_encode($data);
	exit;
}

function humanSize($sz) {
	$units = array('Б', 'КБ', 'МБ', 'ГБ', 'ТБ');
	$power = $sz > 0 ? floor(log($sz, 1024)) : 0;
	return number_format($sz / pow(1024, $power), 2, ',', ' ') . ' ' . $units[$power];
}

function guid() {
	if (function_exists('com_create_guid') === true) {
		return trim(com_create_guid(), '{}');
	}
	$data = openssl_random_pseudo_bytes(16);
	$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
	$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
	return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
