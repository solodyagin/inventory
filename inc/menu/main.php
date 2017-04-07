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

global $rewrite_base;

$this->Add('main', '<i class="fa fa-home fa-fw"></i> Главная', 'Переход на стартовую страницу', 0, '/', "$rewrite_base");
$this->Add('main', '<i class="fa fa-cog fa-fw"></i> Настройка', 'Общая настройка системы', 20, 'config', '');
$this->Add('config', '<i class="fa fa-cog fa-fw"></i> Настройка системы', 'Настройка системы', 0, 'config/config', 'config');
$this->Add('config', '<i class="fa fa-modx fa-fw"></i> Подключенные модули', 'Подключенные модули', 0, 'config/modules', 'modules');
$this->Add('config', '<i class="fa fa-trash fa-fw"></i> Удаление объектов', 'Удаление объектов', 0, 'config/delete', 'delete');
