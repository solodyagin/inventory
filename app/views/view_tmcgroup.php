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
 * Справочники / Группы ТМЦ
 */

# Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$user = User::getInstance();
$cfg = Config::getInstance();

# Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1,4,5,6')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Справочники / Группы ТМЦ"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ", "Добавление", "Редактирование", "Удаление".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="list2"></table>
				<div id="pager2"></div>
				<table id="list10_d"></table>
				<div id="pager10_d"></div>
			</div>
		</div>
	</div>
	<script>
		var addOptions = {
			top: 0, left: 0, width: 500
		};
		$('#list2').jqGrid({
			url: 'route/controller/server/tmc/libre_group.php',
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
			rowNum: 50,
			pager: '#pager2',
			sortname: 'id',
			scroll: 1,
			height: 140,
			viewrecords: true,
			sortorder: 'asc',
			editurl: 'route/controller/server/tmc/libre_group.php',
			caption: 'Группы номенклатуры',
			onSelectRow: function (ids) {
				if (ids == null) {
					ids = 0;
					if ($('#list10_d').jqGrid('getGridParam', 'records') > 0) {
						$('#list10_d').jqGrid('setGridParam', {url: 'route/controller/server/tmc/libre_group_sub.php?q=1&groupid=' + ids, page: 1});
						$('#list10_d').jqGrid('setGridParam', {editurl: 'route/controller/server/tmc/libre_group_sub.php?q=1&groupid=' + ids, page: 1})
										.trigger('reloadGrid');
					}
				} else {
					$('#list10_d').jqGrid('setGridParam', {url: 'route/controller/server/tmc/libre_group_sub.php?q=1&groupid=' + ids, page: 1});
					$('#list10_d').jqGrid('setGridParam', {editurl: 'route/controller/server/tmc/libre_group_sub.php?q=1&groupid=' + ids, page: 1})
									.trigger('reloadGrid');
				}
			}
		});
		$('#list2').jqGrid('setGridHeight', $(window).innerHeight() / 2);
		$('#list2').jqGrid('navGrid', '#pager2', {edit: false, add: true, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});

		$('#list10_d').jqGrid({
			height: 100,
			autowidth: true,
			url: 'route/controller/server/tmc/libre_group_sub.php',
			datatype: 'json',
			colNames: [' ', 'Id', 'Параметр', 'Действия'],
			colModel: [
				{name: 'active', index: 'active', width: 20},
				{name: 'id', index: 'id', width: 55, hidden: true},
				{name: 'name', index: 'name', width: 200, editable: true},
				{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
			],
			rowNum: 5,
			pager: '#pager10_d',
			sortname: 'id',
			scroll: 1,
			viewrecords: true,
			sortorder: 'asc',
			caption: 'Параметры группы номенклатуры'
		}).navGrid('#pager10_d', {add: true, edit: false, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});
	</script>

<?php endif;
