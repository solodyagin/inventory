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

use core\config;

$cfg = config::getInstance();

$this->add('main', '<i class="fas fa-home"></i> Главная', 'Переход на стартовую страницу', 0, '/', "$cfg->rewrite_base");
$this->add('main', '<i class="fas fa-cog"></i> Настройка', 'Общая настройка системы', 20, 'config', '');
$this->add('config', '<i class="fas fa-cog"></i> Настройка системы', 'Настройка системы', 0, 'config/settings', 'settings');
$this->add('config', '<i class="fab fa-modx"></i> Подключенные модули', 'Подключенные модули', 0, 'config/mods', 'mods');
$this->add('config', '<i class="fas fa-trash"></i> Удаление объектов', 'Удаление объектов', 0, 'config/delete', 'delete');
