<?php
/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

/*
 * Настройка / Удаление объектов
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$user = User::getInstance();

// Проверка: если не администратор и не назначена одна из ролей, то
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
			$('#infoblock').load('route/controller/server/delete/delete.php');
			return false;
		});
	</script>

<?php endif;
