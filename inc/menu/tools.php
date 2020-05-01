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

// Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

use core\mod;

$this->add('main', '<i class="fas fa-cog"></i> Инструменты', 'Инструменты', 3, 'tools', '');
$mod = new mod();
$mod->register('workmen', 'Менеджер по обслуживанию ', 'Грибов Павел');
if ($mod->isActive('workmen')) {
	$this->add('tools', '<i class="fas fa-bug"></i> Менеджер по обслуживанию', 'Менеджер по обслуживанию', 3, 'tools/workmen', 'workmen');
}
$this->add('tools', '<i class="fas fa-check"></i> Контроль договоров', 'Контроль договоров', 3, 'tools/dogknt', 'dogknt');
$this->add('tools', '<i class="fas fa-clone"></i> Оргтехника на моём рабочем месте', 'Оргтехника на моём рабочем месте', 3, 'tools/eqlist', 'eqlist');
$mod->register('ping', 'Проверка доступности по ping', 'Грибов Павел');
if ($mod->isActive('ping')) {
	$this->add('tools', '<i class="fas fa-bolt"></i> Проверка доступности', 'Проверка доступности', 3, 'tools/ping', 'ping');
}
unset($mod);
