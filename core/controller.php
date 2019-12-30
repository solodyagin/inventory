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

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

class Controller {

	public $model;
	public $view;

	function __construct() {
		$this->view = new View();
	}

	function index() {
		$view_name = 'view_' . strtolower(str_replace('Controller_', '', get_class($this)));
		$this->view->generate($view_name, '');
	}

}
