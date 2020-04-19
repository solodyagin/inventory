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

$this->Add('main', '<i class="fas fa-list-ul"></i> Справочники', 'Справочники', 10, 'libre', '');
$this->Add('libre', '<i class="fas fa-sitemap"></i> Список организаций', 'Список организаций', 10, 'libre/orglist', 'orglist');
$this->Add('libre', '<i class="fas fa-users"></i> Сотрудники', 'Сотрудники', 10, 'libre/peoples', 'peoples');
$this->Add('libre', '<i class="fas fa-location-arrow"></i> Помещения', 'Помещения', 10, 'libre/places', 'places');
$this->Add('libre', '<i class="fas fa-cogs"></i> Контрагенты', 'Контрагенты', 10, 'libre/knt', 'knt');
$this->Add('libre', '<i class="fas fa-cubes"></i> Производители', 'Производители', 10, 'libre/vendors', 'vendors');
$this->Add('libre', '<i class="fas fa-object-group"></i> Группы номенклатуры', 'Группы номенклатуры', 10, 'libre/nomegroups', 'nomegroups');
$this->Add('libre', '<i class="fab fa-empire"></i> Номенклатура', 'Номенклатура', 10, 'libre/nome', 'nome');
