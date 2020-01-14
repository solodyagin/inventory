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

$this->Add('main', '<i class="fa fa-list-ul fa-fw"></i> Справочники', 'Справочники', 10, 'libre', '');
$this->Add('libre', '<i class="fa fa-sitemap fa-fw"></i> Список организаций', 'Список организаций', 10, 'libre/orglist', 'orglist');
$this->Add('libre', '<i class="fa fa-users fa-fw"></i> Пользователи', 'Пользователи', 10, 'libre/peoples', 'peoples');
$this->Add('libre', '<i class="fa fa-location-arrow fa-fw"></i> Помещения', 'Помещения', 10, 'libre/places', 'places');
$this->Add('libre', '<i class="fa fa-cogs fa-fw"></i> Контрагенты', 'Контрагенты', 10, 'libre/kntlist', 'kntlist');
$this->Add('libre', '<i class="fa fa-cubes fa-fw"></i> Производители', 'Производители', 10, 'libre/kntlist', 'vendors');
$this->Add('libre', '<i class="fa fa-object-group fa-fw"></i> Группы номенклатуры', 'Группы номенклатуры', 10, 'libre/kntlist', 'tmcgroup');
$this->Add('libre', '<i class="fa fa-empire fa-fw"></i> Номенклатура', 'Номенклатура', 10, 'libre/kntlist', 'nome');
