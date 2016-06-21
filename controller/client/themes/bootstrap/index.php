<?php

// Данный код создан и распространяется по лицензии GPL v3
// Изначальный автор данного кода - Грибов Павел
// http://грибовы.рф
// Печатная форма?
if (isset($_GET['printable'])) {
	$printable = $_GET['printable'];
} else {
	$printable = false;
}
// Есть альтернативный заголовок?
if (isset($alterhead)) {
	include_once($alterhead);
} else {
	include_once('header.php');	 // заголовок страницы или из переменной alterhead или стандарный
}
	
// Если не печатная форма, то показываем ВСЁ
if (!$printable) {
	include_once('menus.php');	  // главное меню
	include_once('navbar.php');	  // главное меню
	include_once('messagebar.php'); // отображение сообщений пользователю (если есть)
}

include_once("controller/client/themes/$cfg->theme/$content_page.php");

if (!$printable) {
	include_once('footer.php');	 // подвал страницы    
}
