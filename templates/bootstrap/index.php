<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

// Печатная форма?
$printable = (isset($_GET['printable'])) ? $_GET['printable'] : false;

// Есть альтернативный заголовок?
if (isset($alterhead)) {
	include_once $alterhead;
} else {
	include_once 'assets/header.php';  // заголовок страницы или из переменной alterhead или стандарный
}

// Если не печатная форма, то показываем ВСЁ
if (!$printable) {
	include_once 'assets/menus.php' ;     // главное меню
	include_once 'assets/navbar.php';     // "хлебные крошки"
	include_once 'assets/messagebar.php'; // отображение сообщений пользователю (если есть)
}

echo (isset($view)) ? $view : '';

if (!$printable) {
	include_once 'assets/footer.php';  // подвал страницы
}
