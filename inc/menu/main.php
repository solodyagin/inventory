<?php

/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$this->Add('main', '<i class="fa fa-home fa-fw"> </i>Главная', 'Переход на стартовую страницу', 0, '/', 'home');
$this->Add('main', '<i class="fa fa-cog fa-fw"> </i>Настройка', 'Общая настройка системы', 20, 'config', '');
$this->Add('config', '<i class="fa fa-cog fa-fw"> </i>Настройка системы', 'Настройка системы', 0, 'config/config', 'config');
$this->Add('config', '<i class="fa fa-modx fa-fw"> </i>Подключенные модули', 'Подключенные модули', 0, 'config/modules', 'modules');
$this->Add('config', '<i class="fa fa-trash fa-fw"> </i>Удаление объектов', 'Удаление объектов', 0, 'config/delete', 'delete');
