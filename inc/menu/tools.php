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

$this->Add('main', '<i class="fa fa-cog fa-fw"></i> Инструменты', 'Инструменты', 3, 'tools', '');
$mod = new Mod();
$mod->Register('workmen', 'Менеджер по обслуживанию ', 'Грибов Павел');
if ($mod->IsActive('workmen')) {
	$this->Add('tools', '<i class="fa fa-bug fa-fw"></i> Менеджер по обслуживанию', 'Менеджер по обслуживанию', 3, 'tools/workmen', 'workmen');
}
$this->Add('tools', '<i class="fa fa-check fa-fw"></i> Контроль договоров', 'Контроль договоров', 3, 'tools/dogknt', 'dogknt');
$this->Add('tools', '<i class="fa fa-clone fa-fw"></i> Оргтехника на моём рабочем месте', 'Оргтехника на моём рабочем месте', 3, 'tools/eqlist', 'eqlist');
$mod->Register('ping', 'Проверка доступности по ping', 'Грибов Павел');
if ($mod->IsActive('ping')) {
	$this->Add('tools', '<i class="fa fa-bolt fa-fw"></i> Проверка доступности', 'Проверка доступности', 3, 'tools/ping', 'ping');
}
unset($mod);
