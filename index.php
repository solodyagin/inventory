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

/* Объявляем глобальные переменные */
define('SITE_EXEC', true);
define('SITE_ROOT', dirname(__FILE__));
define('SITE_VERSION', '2020-04-11');
define('SITE_MINIMUM_PHP', '7.0.22');

header('Content-Type: text/html; charset=utf-8');

/* Проверяем версию PHP */
if (version_compare(PHP_VERSION, SITE_MINIMUM_PHP, '<')) {
	die('Для запуска этой версии Inventory ваш хост должен использовать PHP ' . SITE_MINIMUM_PHP . ' или выше!');
}

/* Загружаем движок */
require_once SITE_ROOT . '/bootstrap.php';
