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

$mod = new mod();
$mod->register('cloud', 'Хранилище документов', 'Грибов Павел');
if ($mod->isActive('cloud')) {
	$this->add('main', '<i class="fas fa-cloud"></i> Хранилище документов', 'Хранилище документов', 2, 'cloud', 'cloud');
}
unset($mod);
