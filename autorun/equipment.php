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

$cfg->quickmenu[] = '<div><i class="fa fa-shopping-basket"></i> <a href="index.php?content_page=equipment">Имущество</a></div>';
$cfg->quickmenu[] = '<div><i class="fa fa-tasks"></i> <a href="index.php?content_page=eq_list">ТМЦ на моём рабочем месте</a></div>';
