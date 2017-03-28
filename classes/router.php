<?php

/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

class Router {

	public static $args = []; // Переданные в url параметры

	static function start() {
		$uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
		list($path, $args) = array_pad(explode('?', $uri, 2), 2, null);

		// Получаем параметры
		$query = parse_url($uri, PHP_URL_QUERY);
		if (!empty($query)) {
			parse_str($query, self::$args);
		}

		$routes = explode('/', $path);

		$controller_name = (!empty($routes[1])) ? ucfirst(strtolower($routes[1])) : 'Home';
		$action_name = strtolower(((!empty($routes[2])) ? $routes[2] : 'index'));

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
		switch ($to) {
			case 'error404':
				header('HTTP/1.1 404 Not Found');
				header('Status: 404 Not Found');
				header('Location: /error404');
				break;
			default:
				header("Location: /{$to}");
		}
		exit;
	}

}
