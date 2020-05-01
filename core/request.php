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

class request {

	use singleton;

	public $uri;
	private $vars = [];

	protected function init() {
		$url = filter_input(INPUT_SERVER, 'REQUEST_URI');
		list($this->uri, $query) = array_pad(explode('?', $url, 2), 2, null);
		// Параметры GET
		//$q = parse_url($url, PHP_URL_QUERY);
		//if (!empty($q)) {
		//	parse_str($q, $this->vars);
		//}
		if (!empty($query)) {
			parse_str($query, $this->vars);
		}
		// Параметры POST
		$this->merge($_POST);
	}

	public function set($key, $value) {
		$this->vars[$key] = $value;
	}

	public function get($key, $def = null) {
		if (!isset($this->vars[$key])) {
			return $def;
		}
		return $this->vars[$key];
	}

	public function remove($key) {
		unset($this->vars[$key]);
	}

	public function merge($array) {
		if (is_array($array)) {
			$this->vars = array_merge($this->vars, $array);
		}
	}

}
