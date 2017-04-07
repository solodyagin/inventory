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

$this->Add('main', '<i class="fa fa-list-ul fa-fw"></i> Справочники', 'Справочники', 10, 'libre', '');
$this->Add('libre', '<i class="fa fa-sitemap fa-fw"></i> Список организаций', 'Список организаций', 10, 'libre/orglist', 'orglist');
$this->Add('libre', '<i class="fa fa-users fa-fw"></i> Пользователи', 'Пользователи', 10, 'libre/peoples', 'peoples');
$this->Add('libre', '<i class="fa fa-location-arrow fa-fw"></i> Помещения', 'Помещения', 10, 'libre/places', 'places');
$this->Add('libre', '<i class="fa fa-cogs fa-fw"></i> Контрагенты', 'Контрагенты', 10, 'libre/kntlist', 'kntlist');
$this->Add('libre', '<i class="fa fa-cubes fa-fw"></i> Производители', 'Производители', 10, 'libre/kntlist', 'vendors');
$this->Add('libre', '<i class="fa fa-object-group fa-fw"></i> Группы ТМЦ', 'Группы ТМЦ', 10, 'libre/kntlist', 'tmcgroup');
$this->Add('libre', '<i class="fa fa-empire fa-fw"></i> Номенклатура', 'Номенклатура', 10, 'libre/kntlist', 'nome');
