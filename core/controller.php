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

/**
 * Базовый контроллер
 */
abstract class controller {

	/**
	 * Модель
	 */
	public $model;

	/**
	 * Вид
	 */
	public $view;

	/**
	 * Конструктор класса
	 * @param string $moduleName
	 * @return void 
	 */
	public function __construct($moduleName) {
		$this->view = new view($moduleName);
	}

	/**
	 * Действие, вызываемое по умолчанию
	 */
	public function index() {
		
	}

}
