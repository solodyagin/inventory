<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

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
