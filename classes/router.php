<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

class Router {

	public static $params = []; // Переданные в url GET-параметры

	static function start() {
		global $rewrite_base;
		$uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
		if (strpos($uri, $rewrite_base) === 0) {
			$uri = substr($uri, strlen($rewrite_base));
		}
		list($path, $args) = array_pad(explode('?', $uri, 2), 2, null);

		// Получаем параметры
		$query = parse_url($uri, PHP_URL_QUERY);
		if (!empty($query)) {
			parse_str($query, self::$params);
		}

		$routes = explode('/', $path);

		$controller_name = (!empty($routes[0])) ? ucfirst(strtolower($routes[0])) : 'Home';
		$action_name = strtolower(((!empty($routes[1])) ? $routes[1] : 'index'));

		// Добавляем префикс
		$controller_name = 'Controller_' . $controller_name;
		if (!class_exists($controller_name)) {
			throw new Exception("Undefined controller {$controller_name} referenced");
			//self::redirect('error404');
		}

		// Создаем контроллер
		$controller = new $controller_name();

		// Вызываем действие контроллера
		if (method_exists($controller, $action_name)) {
			$controller->$action_name();
		} else {
			throw new Exception("Undefined action {$action_name} referenced");
			//self::redirect('error404');
		}
	}

	static function redirect($to) {
		global $rewrite_base;
		switch ($to) {
			case 'error404':
				header('HTTP/1.1 404 Not Found');
				header('Status: 404 Not Found');
				break;
		}
		header("Location: {$rewrite_base}{$to}");
		exit();
	}

}
