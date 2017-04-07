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

	public $sitename;   // название сайта
	public $ad;   // использовать для аутентификации Active Directory 0-нет 1-да
	public $domain1;   // домен AD первого уровня (например khortitsa)
	public $domain2;   // домен AD второго уровня (например com)
	public $ldap;   // сервер ldap
	public $theme;  // тема по умолчанию
	public $emailadmin; // от кого будем посылать почту
	public $smtphost;  // сервер SMTP
	public $smtpauth;  // требуется аутентификация?
	public $smtpport, $smtpusername, $smtppass;  // SMTP порт,пользователь,пароль пользователя для входа
	public $emailreplyto; // куда слать ответы
	public $sendemail;  // а вообще будем посылать почту?
	public $version;  // версия платформы
	public $defaultorgid;   // организация "по умолчанию". Выбирается или по кукисам или первая из списка организаций
	public $urlsite;  // где находится сайт http://
	public $navbar = array(); // навигационная последовательность
	public $quickmenu = array(); // "быстрое меню"
	public $style = 'Bootstrap';   //стиль грида по умолчанию
	public $fontsize = '12px';   //стиль грида по умолчанию

	function GetConfigFromBase() {
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
			throw new DBException('Ошибка выполнения Config.GetConfigFromBase', 0, $ex);
		}
	}

	function SetConfigToBase() {
		$sql = <<<TXT
UPDATE config
SET ad = :ad, domain1 = :domain1, domain2 = :domain2, sitename = :sitename,
    theme = :theme, ldap = :ldap, emailadmin = :emailadmin,
    smtphost = :smtphost, smtpauth = :smtpauth, smtpport = :smtpport,
    smtpusername = :smtpusername, smtppass = :smtppass, emailreplyto = :emailreplyto,
    sendemail = :sendemail, urlsite = :urlsite
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
