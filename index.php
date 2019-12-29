<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

# Объявляем глобальные переменные
define('WUO', true);
define('WUO_ROOT', dirname(__FILE__));
define('WUO_VERSION', '1912');
define('WUO_MINIMUM_PHP', '7.0.22');

header('Content-Type: text/html; charset=utf-8');

# Проверяем версию PHP
if (version_compare(PHP_VERSION, WUO_MINIMUM_PHP, '<')) {
	die('Для запуска этой версии CMS ваш хост должен использовать PHP ' . WUO_MINIMUM_PHP . ' или выше!');
}

# Загружаем движок
include_once WUO_ROOT . '/bootstrap.php';
