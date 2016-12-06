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
 * Настройка / Подключенные модули
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

// Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (($user->mode != 1) && (!$user->TestRoles('1'))):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Настройка / Подключенные модули"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="list2"></table>
				<div id="pager2"></div>
				<script src="controller/client/js/mdconfig.js"></script>
			</div>
		</div>
	</div>

<?php endif;
