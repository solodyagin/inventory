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

namespace core;

use PDOException;
use core\db;
use core\dbexception;

class utils {

	/**
	 * Возвращает массив из папок в указанной папке
	 * @param string $dir
	 * @return array
	 */
	public static function getArrayFilesInDir($dir) {
		$includes_dir = opendir($dir);
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
	public static function checkLDAPuser($username, $password, $ladpserver, $domain1, $domain2) {
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
	public static function getRandomId($n = 60) {
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
	public static function getRandomDigits($n = 60) {
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
	public static function DateToMySQLDateTime2($dt) {
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
	public static function MySQLDateTimeToDateTime($dt) {
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
	public static function MySQLDateToDate($dt) {
		$str1 = explode('-', $dt);
		$dtt = $str1[2] . '.' . $str1[1] . '.' . $str1[0];
		return $dtt;
	}

	/**
	 * Преобразует дату MySQL 2012-01-01 00:00:00 в dd.mm.2012
	 * @param type $dt
	 * @return string
	 */
	public static function MySQLDateTimeToDateTimeNoTime($dt) {
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
	public static function getStiker() {
		$stik['body'] = '';
		$stik['title'] = '';
		try {
			$sql = 'select * from news where stiker = 1 order by id limit 1';
			$row = db::prepare($sql)->execute()->fetch();
			if ($row) {
				$stik['body'] = $row['body'];
				$stik['title'] = $row['title'];
			}
		} catch (PDOException $ex) {
			throw new dbexception('Неверный запрос getstiker', 0, $ex);
		}
		return $stik;
	}

	/**
	 * Возвращает массив активных организаций
	 * @return array
	 * @throws Exception
	 */
	public static function getArrayOrgs() {
		$mOrgs = [];
		$sql = 'select * from org where active = 1 order by name';
		try {
			$cnt = 0;
			$arr = db::prepare($sql)->execute()->fetchAll();
			foreach ($arr as $row) {
				$mOrgs[$cnt]['id'] = $row['id'];
				$mOrgs[$cnt]['name'] = $row['name'];
				$mOrgs[$cnt]['picnmap'] = $row['picmap'];
				$mOrgs[$cnt]['active'] = $row['active'];
				$cnt++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Неверный запрос getArrayOrgs', 0, $ex);
		}
		return $mOrgs;
	}

	/**
	 * Возвращает массив активных организаций
	 * @return type
	 * @throws DBException
	 */
	public static function getArrayKnt() {
		$mOrgs = [];
		try {
			$cnt = 0;
			$sql = 'select * from knt where active = 1 order by name';
			$arr = db::prepare($sql)->execute()->fetchAll();
			foreach ($arr as $row) {
				$mOrgs[$cnt]['id'] = $row['id'];
				$mOrgs[$cnt]['name'] = $row['name'];
				$mOrgs[$cnt]['active'] = $row['active'];
				$cnt++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Неверный запрос getArrayKnt', 0, $ex);
		}
		return $mOrgs;
	}

	public static function mailq($to, $subject, $content, $attach = false) {
		$cfg = config::getInstance();
		sendMailAttachment($to, $cfg->smtpusername, $subject, $content);
	}

	public static function sendMailAttachment($mailTo, $from, $subject, $message, $file = false) {
		$ffn = basename($file);
		$separator = "---"; // разделитель в письме
		// Заголовки для письма
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "From: $from\nReply-To: $from\r\n"; // задаем от кого письмо
		//$headers .= "Content-Type: text/html; charset=utf-8\r\n";
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
	public static function smtpmail($to, $subject, $content, $attach = false) {
		$sql = "insert into mailq (id, `from`, `to`, `title`, btxt) values (null, '', :to, :subject, :content)";
		try {
			db::prepare($sql)->execute([
				':to' => $to,
				':subject' => $subject,
				':content' => $content
			]);
		} catch (PDOException $ex) {
			throw new dbexception('Не удалось записать очередь сообщений', 0, $ex);
		}
	}

	/**
	 * Проверяет есть ли дубли логинов в базе. Результат - количество логинов
	 * @param string $login
	 * @return integer
	 * @throws DBException
	 */
	public static function doubleLogin($login) {
		$cnt = 0;
		try {
			$sql = 'select count(id) as cnt from users where login = :login';
			$row = db::prepare($sql)->execute([':login' => $login])->fetch();
			if ($row) {
				$cnt = $row['cnt'];
			}
		} catch (PDOException $ex) {
			throw new dbexception('Неверный запрос doubleLogin', 0, $ex);
		}
		return $cnt;
	}

	/**
	 * Проверяет есть ли дубли логинов в базе. Результат - количество логинов
	 * @param string $email
	 * @return integer
	 * @throws DBException
	 */
	public static function doubleEmail($email) {
		$cnt = 0;
		try {
			$sql = 'select count(id) as cnt from users where email = :email';
			$row = db::prepare($sql)->execute([':email' => $email])->fetch();
			if ($row) {
				$cnt = $row['cnt'];
			}
		} catch (PDOException $ex) {
			throw new dbexception('Неверный запрос doubleEmail', 0, $ex);
		}
		return $cnt;
	}

	public static function reUpdateRepairEq() {
		try {
			// листаем весь список ТМЦ
			$rows = db::prepare('select * from equipment')->execute()->fetchAll();
			foreach ($rows as $row) {
				$uid = $row['id'];
				$rs = 0;
				// Для каждого ТМЦ проверяем "что у нас с ремонтами"
				$row2 = db::prepare('select * from repair where eqid = :uid order by id desc limit 1')->execute([':uid' => $uid])->fetch();
				if ($row2) {
					$rs = $row2['status'];
				}
				db::prepare('update equipment set repair = :rs where id= :uid')->execute([':rs' => $rs, ':uid' => $uid]);
			}
		} catch (PDOException $ex) {
			throw new dbexception('Неверный запрос reUpdateRepairEq', 0, $ex);
		}
	}

	/**
	 * Генерирует пароль
	 * @param type $number
	 * @return string
	 */
	public static function generatePassword($number) {
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

	public static function generateEAN($number) {
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

	public static function generateSalt() {
		$salt = '';
		$length = rand(5, 10); // длина соли (от 5 до 10 сомволов)
		for ($i = 0; $i < $length; $i++) {
			$salt .= chr(rand(33, 126)); // символ из ASCII-table
		}
		return $salt;
	}

	public static function jsonExit($data) {
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($data);
		exit;
	}

	public static function humanSize($sz) {
		$units = ['Б', 'КБ', 'МБ', 'ГБ', 'ТБ'];
		$power = $sz > 0 ? floor(log($sz, 1024)) : 0;
		return number_format($sz / pow(1024, $power), 2, ',', ' ') . ' ' . $units[$power];
	}

	public static function guid() {
		if (function_exists('com_create_guid') === true) {
			return trim(com_create_guid(), '{}');
		}
		$data = openssl_random_pseudo_bytes(16);
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

}
