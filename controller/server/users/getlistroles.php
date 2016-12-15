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
 * Роли: http://грибовы.рф/wiki/doku.php/основы:доступ:роли
 */
echo <<<TXT
<select name="rolesusers" id="rolesusers">
	<option value="1">Полный доступ</option>
	<option value="2">Просмотр финансовых отчетов</option>
	<option value="3">Просмотр</option>
	<option value="4">Добавление</option>
	<option value="5">Редактирование</option>
	<option value="6">Удаление</option>
	<option value="7">Отправка СМС</option>
	<option value="8">Манипуляции с деньгами</option>
	<option value="9">Редактирование карт</option>
</select>
TXT;
