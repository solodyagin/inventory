<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

class Config {

	use Singleton;

	public $debug; // Режим отладки
	public $db_host; // Хост БД
	public $db_user; // Пользователь БД
	public $db_pass; // Пароль пользователя БД
	public $db_name; // Имя БД
	public $db_char; // Кодировка БД
	public $rewrite_base; // Размещение системы относительно корня сайта
	public $sitename; // Название сайта
	public $ad;   // Использовать для аутентификации Active Directory 0-нет, 1-да
	public $domain1;   // Домен AD первого уровня (например khortitsa)
	public $domain2;   // Домен AD второго уровня (например com)
	public $ldap;   // Сервер ldap, включая протокол ldap:// или ldaps://
	public $theme;  // Шаблон по умолчанию
	public $emailadmin; // От кого будем посылать почту
	public $smtphost;  // Сервер SMTP
	public $smtpauth;  // Требуется аутентификация?
	public $smtpport, $smtpusername, $smtppass;  // SMTP порт,пользователь,пароль пользователя для входа
	public $emailreplyto; // Куда слать ответы
	public $sendemail;  // А вообще будем посылать почту?
	public $version;  // Версия платформы
	public $defaultorgid;   // Организация "по умолчанию". Выбирается или по кукисам или первая из списка организаций
	public $urlsite;  // Где находится сайт http://
	public $navbar = array(); // Навигационная последовательность
	public $quickmenu = array(); // "Быстрое меню"
	public $style = 'Bootstrap';   // Стиль грида по умолчанию
	public $fontsize = '12px';   // Стиль грида по умолчанию

	/**
	 *  Получает настройки из файла конфигурации
	 */
	function loadFromFile() {
		$res = include_once(WUO_ROOT . '/config.php');
		if ($res) {
			$this->debug = $debug; // Режим отладки
			$this->db_host = $mysql_host; // Хост БД
			$this->db_user = $mysql_user; // Пользователь БД
			$this->db_pass = $mysql_pass; // Пароль пользователя БД
			$this->db_name = $mysql_base; // Имя базы
			$this->db_char = $mysql_char; // Кодировка базы
			$this->rewrite_base = $rewrite_base; // Размещение системы относительно корня сайта
		}
		return $res;
	}

	function loadFromDB() {
		try {
			$row = DB::prepare('SELECT * FROM config')->execute()->fetch();
			if ($row) {
				$this->ad = $row['ad'];
				$this->domain1 = $row['domain1'];
				$this->domain2 = $row['domain2'];
				$this->sitename = htmlspecialchars($row['sitename'], ENT_QUOTES);
				$this->ldap = $row['ldap'];
				$this->theme = $row['theme'];
				$this->emailadmin = htmlspecialchars($row['emailadmin']);
				$this->smtphost = $row['smtphost'];  // сервер SMTP
				$this->smtpauth = $row['smtpauth'];  // требуется утенфикация?
				$this->smtpport = $row['smtpport'];  // SMTP порт
				$this->smtpusername = stripslashes($row['smtpusername']); // SMTP имя пользователя для входа
				$this->smtppass = stripslashes($row['smtppass']);  // SMTP пароль пользователя для входа
				$this->emailreplyto = stripslashes($row['emailreplyto']); // куда слать ответы
				$this->sendemail = $row['sendemail'];  // а вообще будем посылать почту?
				$this->version = $row['version'];
				$this->urlsite = $row['urlsite'];
				$this->style = (isset($_COOKIE['stl'])) ? $_COOKIE['stl'] : 'Bootstrap';
				$this->fontsize = (isset($_COOKIE['fontsize'])) ? $_COOKIE['fontsize'] : '12px';
			}
			if (isset($_COOKIE['defaultorgid'])) {
				$this->defaultorgid = $_COOKIE['defaultorgid'];
			} else {
				$row = DB::prepare('SELECT * FROM org WHERE active = 1 ORDER BY id ASC LIMIT 1')->execute()->fetch();
				if ($row) {
					$this->defaultorgid = $row['id'];
				}
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Config.loadFromDB()', 0, $ex);
		}
	}

	function saveToDB() {
		$sql = <<<TXT
UPDATE	config
SET		ad = :ad,
		domain1 = :domain1,
		domain2 = :domain2,
		sitename = :sitename,
		theme = :theme,
		ldap = :ldap,
		emailadmin = :emailadmin,
		smtphost = :smtphost,
		smtpauth = :smtpauth,
		smtpport = :smtpport,
		smtpusername = :smtpusername,
		smtppass = :smtppass,
		emailreplyto = :emailreplyto,
		sendemail = :sendemail,
		urlsite = :urlsite
TXT;
		try {
			DB::prepare($sql)->execute(array(
				':ad' => $this->ad,
				':domain1' => $this->domain1,
				':domain2' => $this->domain2,
				':sitename' => $this->sitename,
				':theme' => $this->theme,
				':ldap' => $this->ldap,
				':emailadmin' => $this->emailadmin,
				':smtphost' => $this->smtphost,
				':smtpauth' => $this->smtpauth,
				':smtpport' => $this->smtpport,
				':smtpusername' => $this->smtpusername,
				':smtppass' => $this->smtppass,
				':emailreplyto' => $this->emailreplyto,
				':sendemail' => $this->sendemail,
				':urlsite' => $this->urlsite
			));
			return true;
		} catch (PDOException $ex) {
			return false;
		}
	}

}
