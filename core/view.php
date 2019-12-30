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

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

class View {

	function generate($content_view, $template_view, $data = null) {
		if (is_array($data)) {
			extract($data);
		}

		# Подключаем файл вида
		$file = SITE_ROOT . "/app/views/{$content_view}.php";
		if (file_exists($file)) {
			if (!empty($template_view)) {
				ob_start();
				require_once $file;
				$view = ob_get_clean();
			} else {
				require_once $file;
			}
		}

		# Подключаем общий шаблон
		if (!empty($template_view)) {
			$file = SITE_ROOT . "/templates/{$template_view}/index.php";
			if (file_exists($file)) {
				require_once $file;
			}
		}
	}

}
