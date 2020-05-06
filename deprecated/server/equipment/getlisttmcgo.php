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

use core\config;
use core\request;
use core\user;

$cfg = config::getInstance();
$orgid = $cfg->defaultorgid;

$req = request::getInstance();
$addnone = $req->get('addnone');

$user = user::getInstance();
if ($user->isAdmin() || $user->testRights([1, 4, 5, 6])) {
	echo '<select name="tmcgo" id="tmcgo">';
	if ($addnone == 'true') {
		echo '<option value="-1">не выбрано</option>';
	}
	echo '<option value="0">На месте</option>';
	echo '<option value="1">В пути</option>';
	echo '</select>';
} else {
	echo 'Недостаточно прав';
}
