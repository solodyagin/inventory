<?php

/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$md = new Tmod; // обьявляем переменную для работы с классом модуля
$this->Add('main', '<i class="fa fa-hashtag fa-fw"> </i>Журналы', 'Журналы', 3, 'doc', '');
if ($md->IsActive('news') == 1) {
	$this->Add('doc', '<i class="fa fa-newspaper-o fa-fw"> </i>Новости', 'Новости', 3, 'doc/news', 'news');
}
$this->Add('doc', '<i class="fa fa-empire fa-fw"> </i>Имущество', 'Имущество', 3, 'doc/equipment', 'equipment');
unset($md);
