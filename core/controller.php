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

//namespace Core;

class Controller {

	public $model;
	public $view;

	function __construct() {
		$this->view = new View();
	}

	function index() {
		$view_name = strtolower(str_replace('Controller_', '', get_class($this))) . '/index';
		$this->view->render($view_name);
	}

}
