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
 * Инструменты / Менеджер по обслуживанию
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

// Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (($user->mode != 1) && (!$user->TestRoles('1,3,4,5,6'))):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Инструменты / Менеджер по обслуживанию"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ", "Просмотр", "Добавление", "Редактирование", "Удаление".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<div class="row-fluid">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="workmen"></table>
				<div id="workmen_footer"></div>
				<div id="pg_add_edit"></div>
				<div class="row-fluid">
					<div class="col-xs-2 col-md-2 col-sm-2">
						<div id="photoid"></div>
					</div>
					<div class="col-xs-10 col-md-10 col-sm-10">
						<table id="tbl_rep"></table>
						<div id="rp_nav"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<form method="post" action="inc/csvExport.php">
		<input type="hidden" name="csvBuffer" id="csvBuffer" value="">
	</form>
	<script src="controller/client/js/workmen.js"></script>

<?php endif;
