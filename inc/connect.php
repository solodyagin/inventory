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

/*
 * Проверяем соединение с MySQL и получаем base_id, который используем в дальнейшем
 * во всем портале для соединения с базой
 */
$sqlcn = new Tsql();
$sqlcn->connect($mysql_host, $mysql_user, $mysql_pass, $mysql_base);
