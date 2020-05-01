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

use Exception;

/**
 * Маршрутизатор
 * См. https://github.com/daveh/php-mvc
 */
class router {

	use singleton;

	/**
	 * Ассоциативный массив маршрутов (таблица маршрутизации)
	 * @var array
	 */
	protected $routes = [];

	/**
	 * Параметры маршрута, полученные после проверки соответствия по таблице маршрутов
	 * @var array
	 */
	protected $params = [];

	/**
	 * Добавляет маршрут в таблицу маршрутизации
	 * @param string $route  URI маршрута с ведущим слэшем
	 * @param array  $params Параметры (controller, action, др.)
	 * @return void
	 */
	public function add($route, $params = []) {
		// Convert the route to a regular expression: escape forward slashes
		$route = preg_replace('/\//', '\\/', $route);
		// Convert variables e.g. {controller}
		$route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
		// Convert variables with custom regular expressions e.g. {id:\d+}
		$route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
		// Add start and end delimiters, and case insensitive flag
		$route = '/^' . $route . '$/i';
		$this->routes[$route] = $params;
	}

	/**
	 * Подбирает маршрут в таблице маршрутизации, если маршрут найден, то устанавливает значение переменной $params
	 * @param string $uri URI маршрута с ведущим слэшем
	 * @return boolean
	 */
	public function match($uri) {
		foreach ($this->routes as $route => $params) {
			if (preg_match($route, $uri, $matches)) {
				// Get named capture group values
				foreach ($matches as $key => $match) {
					if (is_string($key)) {
						$params[$key] = $match;
					}
				}
				$this->params = $params;
				return true;
			}
		}
		return false;
	}

	/**
	 * Обрабатывает маршрут, создаёт объект контроллера и запускает его метод
	 * @return void
	 */
	public function dispatch() {
		$req = request::getInstance();
		if (!$this->match($req->uri)) {
			throw new Exception("No route matched for uri '{$req->uri}'", 404);
		}
		$req->merge($this->params);

		$moduleName = $this->params['module'] ?? '';
		$controllerName = $this->params['controller'] ?? 'main';
		$actionName = $this->params['action'] ?? 'index';

		// Добавляем префикс
		if ($moduleName == '') {
			$controllerFullName = $this->getNamespace() . $controllerName;
		} else {
			$controllerFullName = "modules\\$moduleName\\controllers\\$controllerName";
		}
		if (!class_exists($controllerFullName)) {
			throw new Exception("Undefined controller '$controllerFullName' referenced");
		}

		// Создаем контроллер
		$controller = new $controllerFullName($moduleName);

		// Вызываем метод контроллера
		if (!method_exists($controller, $actionName)) {
			throw new Exception("Undefined action '$actionName' referenced");
		}

		$controller->$actionName();
	}

	/**
	 * Get the namespace for the controller class. The namespace defined in the
	 * route parameters is added if present.
	 *
	 * @return string The request URL
	 */
	protected function getNamespace() {
		$namespace = 'app\\controllers\\';
		if (array_key_exists('namespace', $this->params)) {
			$namespace .= $this->params['namespace'] . '\\';
		}
		return $namespace;
	}

	/**
	 * Перенаправляет на страницу
	 * @param string $to
	 */
	static function redirect($to) {
		$cfg = config::getInstance();
		switch ($to) {
			case 'error404':
				header('HTTP/1.1 404 Not Found');
				header('Status: 404 Not Found');
				break;
		}
		header("Location: {$cfg->rewrite_base}$to");
		exit();
	}

}
