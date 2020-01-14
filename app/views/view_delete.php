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

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

/*
 * Настройка / Удаление объектов
 */

$user = User::getInstance();

# Проверка: если не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Настройка / Удаление объектов"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ".
	</div>

<?php else: ?>

	<div class="well">
		<button name="bdel" id="bdel" class="btn btn-primary">Начать удаление</button>
		<div id="infoblock"></div>
	</div>
	<script>
		$('#bdel').click(function () {
			$('#infoblock').load('route/deprecated/server/delete/delete.php');
			return false;
		});
	</script>

<?php endif;
