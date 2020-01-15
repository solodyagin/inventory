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

/* Запрещаем прямой вызов скрипта. */
defined('SITE_EXEC') or die('Доступ запрещён');

$this->Add('main', '<i class="fas fa-hashtag"></i> Журналы', 'Журналы', 3, 'doc', '');
$mod = new Mod();
if ($mod->IsActive('news')) {
	$this->Add('doc', '<i class="fas fa-newspaper"></i> Новости', 'Новости', 3, 'doc/news', 'news');
}
unset($mod);
$this->Add('doc', '<i class="fab fa-empire"></i> Имущество', 'Имущество', 3, 'doc/equipment', 'equipment');
