<?php

/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

// Печатная форма?
$printable = (isset($_GET['printable'])) ? $_GET['printable'] : false;

// Есть альтернативный заголовок?
if (isset($alterhead)) {
	include_once($alterhead);
} else {
	include_once('header.php');  // заголовок страницы или из переменной alterhead или стандарный
}

// Если не печатная форма, то показываем ВСЁ
if (!$printable) {
	include_once('menus.php');   // главное меню
	include_once('navbar.php');   // главное меню
	include_once('messagebar.php'); // отображение сообщений пользователю (если есть)
}

echo (isset($view)) ? $view : '';

if (!$printable) {
	include_once('footer.php');  // подвал страницы    
}
