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

class Config {

	use Singleton;

	var $sitename;   // название сайта
	var $ad;   // использовать для аутенфикации Active Directory 0-нет 1-да
	var $domain1;   // домен AD первого уровня (например khortitsa)
	var $domain2;   // домен AD второго уровня (например com)
	var $ldap;   // сервер ldap
	var $usercanregistrate; // может ли пользователь регистрироваться на сайте сам 1-да
	var $useraddfromad;  // добавлять ли учетку автоматически при заходе пользователь через AD. 1-да
	var $theme;  // тема по умолчанию
	var $emailadmin; // от кого будем посылать почту
	var $smtphost;  // сервер SMTP
	var $smtpauth;  // требуется утенфикация?
	var $smtpport, $smtpusername, $smtppass;  // SMTP порт,пользователь,пароль пользователя для входа
	var $emailreplyto; // куда слать ответы
	var $sendemail;  // а вообще будем посылать почту?
	var $version;  // версия платформы
	var $defaultorgid;   // организация "по умолчанию". Выбирается или по кукисам или первая из списка организаций
	var $urlsite;  // где находится сайт http://
	var $navbar = array(); // навигационная последовательность
	var $quickmenu = array(); // "быстрое меню"
	var $style = 'Bootstrap';   //стиль грида по умолчанию
	var $fontsize = '12px';   //стиль грида по умолчанию 

	function GetConfigFromBase() {
		try {
			$arr = DB::prepare('SELECT * FROM config')->execute()->fetchAll();
			foreach ($arr as $row) {
				$this->ad = $row['ad'];
				$this->domain1 = $row['domain1'];
				$this->domain2 = $row['domain2'];
				$this->sitename = htmlspecialchars($row['sitename'], ENT_QUOTES);
				$this->usercanregistrate = $row['usercanregistrate'];
				$this->useraddfromad = $row['useraddfromad'];
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
    theme = :theme, usercanregistrate = '1', ldap = :ldap, emailadmin = :emailadmin,
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
