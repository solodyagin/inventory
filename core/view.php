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

class view {

	private $moduleName;

	public function __construct($moduleName) {
		$this->moduleName = $moduleName;
	}

	public function render($content_view, $data = null) {
		if (is_array($data)) {
			extract($data);
		}
		// Подключаем файл вида
		$file = SITE_ROOT . "/app/views/$content_view.php";
		if (file_exists($file)) {
			require_once $file;
		}
	}

	public function renderTemplate($content_view, $data = null) {
		if (is_array($data)) {
			extract($data);
		}
		// Подключаем файл вида
		$file = SITE_ROOT . "/app/views/$content_view.php";
		if (file_exists($file)) {
			ob_start();
			require_once $file;
			$view = ob_get_clean();
		}
		// Подключаем общий шаблон
		$file = SITE_ROOT . "/app/views/layout.php";
		if (file_exists($file)) {
			require_once $file;
		}
	}

}
