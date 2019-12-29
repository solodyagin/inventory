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
 * Справочники / Производители
 */

# Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$user = User::getInstance();
$cfg = Config::getInstance();

# Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Справочники / Производители"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="list2"></table>
				<div id="pager2"></div>
			</div>
		</div>
	</div>
	<script>
		$('#list2').jqGrid({
			url: 'route/controller/server/tmc/libre_vendor.php',
			datatype: 'json',
			colNames: [' ', 'Id', 'Имя', 'Комментарий', 'Действия'],
			colModel: [
				{name: 'active', index: 'active', width: 20},
				{name: 'id', index: 'id', width: 55, hidden: true},
				{name: 'name', index: 'name', width: 200, editable: true},
				{name: 'comment', index: 'comment', width: 200, editable: true},
				{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
			],
			autowidth: true,
			pager: '#pager2',
			sortname: 'id',
			scroll: 1,
			viewrecords: true,
			sortorder: 'asc',
			editurl: 'route/controller/server/tmc/libre_vendor.php',
			caption: 'Справочник производителей'
		});
		var addOptions = {
			top: 0, left: 0, width: 500
		};
		$('#list2').jqGrid('setGridHeight', $(window).innerHeight() / 2);
		$('#list2').jqGrid('navGrid', '#pager2', {edit: false, add: true, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});
	</script>

<?php endif;
