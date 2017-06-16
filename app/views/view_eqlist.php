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
 * Инструменты / ТМЦ на моём рабочем месте
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$user = User::getInstance();
$cfg = Config::getInstance();

// Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1,3,4,5,6')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Инструменты / ТМЦ на моём рабочем месте"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ", "Просмотр", "Добавление", "Редактирование", "Удаление".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<div class="row-fluid">
			<ul class="nav nav-tabs" id="myTab">
				<li><a href="#plc" data-toggle="tab">Помещение</a></li>
				<li><a href="#mto" data-toggle="tab">Ответственность</a></li>
			</ul>
		</div>
		<div class="row-fluid">
			<div class="col-xs-2 col-md-2 col-sm-2">
				<div id="photoid" name="photoid" align="center">
					<img src="templates/<?= $cfg->theme; ?>/img/noimage.jpg" width="200">
				</div>
				<input name="geteqid" type="hidden" id="geteqid" value="">
			</div>
			<div class="col-xs-10 col-md-10 col-sm-10">
				<table id="list2"></table>
				<div id="pager2"></div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="tbl_move"></table>
				<div id="pager4"></div>
			</div>
		</div>
	</div>
	<script src="templates/<?= $cfg->theme; ?>/assets/js/eqlist.js"></script>

<?php endif;
