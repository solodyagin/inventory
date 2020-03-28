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

class Router {

	public static $params = []; # Переданные в url GET-параметры

	static function start() {
		$cfg = Config::getInstance();

		$uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
		if (strpos($uri, $cfg->rewrite_base) === 0) {
			$uri = substr($uri, strlen($cfg->rewrite_base));
		}
		list($path, $args) = array_pad(explode('?', $uri, 2), 2, null);

		/* Получаем параметры */
		$query = parse_url($uri, PHP_URL_QUERY);
		if (!empty($query)) {
			parse_str($query, self::$params);
		}

		$routes = explode('/', $path);

		$controller_name = (!empty($routes[0])) ? ucfirst(strtolower($routes[0])) : 'Main';
		$action_name = strtolower(((!empty($routes[1])) ? $routes[1] : 'index'));

		/* Добавляем префикс */
		$controller_name = 'Controller_' . $controller_name;
		if (!class_exists($controller_name)) {
			throw new Exception("Undefined controller {$controller_name} referenced");
			//self::redirect('error404');
		}

		/* Создаем контроллер */
		$controller = new $controller_name();

		/* Вызываем действие контроллера */
		if (method_exists($controller, $action_name)) {
			$controller->$action_name();
		} else {
			throw new Exception("Undefined action {$action_name} referenced");
			//self::redirect('error404');
		}
	}

	static function redirect($to) {
		$cfg = Config::getInstance();
		switch ($to) {
			case 'error404':
				header('HTTP/1.1 404 Not Found');
				header('Status: 404 Not Found');
				break;
		}
		header("Location: {$cfg->rewrite_base}{$to}");
		exit();
	}

}
