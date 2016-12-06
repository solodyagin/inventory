<?php
/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

/*
 * Инструменты / Проверка доступности ТМЦ
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

// Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (($user->mode != 1) && (!$user->TestRoles('1'))):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Инструменты / Проверка доступности ТМЦ"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ".
	</div>

<?php else: ?>

	<div class="well">
		<input id="test_ping" class="btn btn-primary" name="test_ping" value="Проверить">
		<div id="ping_add"></div>
	</div>
	<script src="controller/client/js/ping.js"></script>

<?php endif;
