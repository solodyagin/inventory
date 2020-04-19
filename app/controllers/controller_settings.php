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

class Controller_Settings extends Controller {

	function index() {
		$user = User::getInstance();
		$cfg = Config::getInstance();
		$data['section'] = 'Настройка / Настройка системы';
		if ($user->isAdmin()) {
			$this->view->generate('settings/index', $cfg->theme, $data);
		} else {
			$this->view->generate('restricted', $cfg->theme, $data);
		}
	}

	function save() {
		global $ok, $err;
		$user = User::getInstance();
		if (!$user->isAdmin()) {
			die('Настройки может сохранять только администратор!');
		}
		$cfg = Config::getInstance();
		$cfg->sitename = filter_input(INPUT_POST, 'form_sitename');
		$opt = ['options' => ['default' => 0]];
		$cfg->ad = filter_input(INPUT_POST, 'form_cfg_ad', FILTER_VALIDATE_INT, $opt);
		$cfg->ldap = filter_input(INPUT_POST, 'form_cfg_ldap');
		$cfg->domain1 = filter_input(INPUT_POST, 'form_cfg_domain1');
		$cfg->domain2 = filter_input(INPUT_POST, 'form_cfg_domain2');
		$cfg->theme = filter_input(INPUT_POST, 'form_cfg_theme_sl');
		$cfg->emailadmin = filter_input(INPUT_POST, 'form_emailadmin');
		$cfg->smtphost = filter_input(INPUT_POST, 'form_smtphost'); # Сервер SMTP
		$cfg->smtpauth = filter_input(INPUT_POST, 'form_smtpauth', FILTER_VALIDATE_INT, $opt); # Требуется аутентификация?
		$cfg->smtpport = filter_input(INPUT_POST, 'form_smtpport'); # SMTP порт
		$cfg->smtpusername = filter_input(INPUT_POST, 'form_smtpusername');  # SMTP имя пользователя для входа
		$cfg->smtppass = filter_input(INPUT_POST, 'form_smtppass'); # SMTP пароль пользователя для входа
		$cfg->emailreplyto = filter_input(INPUT_POST, 'form_emailreplyto');  # Куда слать ответы
		$cfg->urlsite = filter_input(INPUT_POST, 'urlsite');  # А где сайт находится?
		$cfg->sendemail = filter_input(INPUT_POST, 'form_sendemail', FILTER_VALIDATE_INT, $opt); # А вообще будем посылать почту?
		$res = $cfg->saveToDB();
		if ($res) {
			$ok[] = 'Успешно сохранено!';
		} else {
			$err[] = 'Что-то пошло не так!';
		}
		/* Подключаем шаблон */
		$this->index();
	}

}
