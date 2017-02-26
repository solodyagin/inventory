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

$md = new Mod(); // обьявляем переменную для работы с классом модуля

$this->Add('main', '<i class="fa fa-cog fa-fw"></i> Инструменты', 'Инструменты', 3, 'tools', '');

$md->Register('workmen', 'Менеджер по обслуживанию ', 'Грибов Павел');
if ($md->IsActive('workmen') == 1) {
	$this->Add('tools', '<i class="fa fa-bug fa-fw"></i> Менеджер по обслуживанию', 'Менеджер по обслуживанию', 3, 'tools/workmen', 'workmen');
}

$this->Add('tools', '<i class="fa fa-check fa-fw"></i> Контроль договоров', 'Контроль договоров', 3, 'tools/dog_knt', 'dog_knt');
$this->Add('tools', '<i class="fa fa-clone fa-fw"></i> ТМЦ на моём рабочем месте', 'ТМЦ на моём рабочем месте', 3, 'tools/eq_list', 'eq_list');

$md->Register('ping', 'Проверка доступности ТМЦ по ping', 'Грибов Павел');
if ($md->IsActive('ping') == 1) {
	$this->Add('tools', '<i class="fa fa-bolt fa-fw"></i> Проверка доступности ТМЦ', 'Проверка доступности ТМЦ', 3, 'tools/ping', 'ping');
}

unset($md);
