<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

class View {

	function generate($content_view, $template_view, $data = null) {
		if (is_array($data)) {
			extract($data);
		}

		// Подключаем файл вида
		$file = WUO_ROOT . "/app/views/{$content_view}.php";
		if (file_exists($file)) {
			if (!empty($template_view)) {
				ob_start();
				require_once $file;
				$view = ob_get_clean();
			} else {
				require_once $file;
			}
		}

		// Подключаем общий шаблон
		if (!empty($template_view)) {
			$file = WUO_ROOT . "/templates/{$template_view}/index.php";
			if (file_exists($file)) {
				require_once $file;
			}
		}
	}

}
