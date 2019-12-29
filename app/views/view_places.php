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
 * Справочники / Помещения
 */

# Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$user = User::getInstance();
$cfg = Config::getInstance();

# Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Справочники / Помещения"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="list2"></table>
				<div id="pager2"></div>
				<table id="list10_d"></table>
				<div id="pager10_d"></div>
				<div id="add_edit"></div>
			</div>
		</div>
	</div>
	<script>
		function GetGrid() {
			$('#list2').jqGrid({
				url: 'places/get?orgid=' + defaultorgid,
				datatype: 'json',
				colNames: [' ', 'Id', 'Подразделение', 'Наименование', 'Комментарий', 'Действия'],
				colModel: [
					{name: 'active', index: 'active', width: 10},
					{name: 'id', index: 'id', width: 55, hidden: true},
					{name: 'opgroup', index: 'opgroup', width: 100, editable: true},
					{name: 'name', index: 'name', width: 200, editable: true},
					{name: 'comment', index: 'comment', width: 200, editable: true},
					{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
				],
				grouping: true,
				groupingView: {
					groupField: ['opgroup'],
					groupColumnShow: [true],
					groupText: ['<b>{0}</b>'],
					groupOrder: ['asc'],
					groupSummary: [false],
					groupCollapse: false

				},
				autowidth: true,
				rowNum: 20,
				pager: '#pager2',
				sortname: 'id',
				scroll: 1,
				height: 140,
				viewrecords: true,
				sortorder: 'asc',
				editurl: 'places/change?orgid=' + defaultorgid,
				caption: 'Помещения',
				onSelectRow: function (ids) {
					GetSubGrid();
					if (ids == null) {
						ids = 0;
						if ($('#list10_d').jqGrid('getGridParam', 'records') > 0) {
							$('#list10_d').jqGrid('setGridParam', {url: 'places/getsub?placesid=' + ids + '&orgid=' + defaultorgid});
							$('#list10_d').jqGrid('setGridParam', {editurl: 'places/changesub?placesid=' + ids + '&orgid=' + defaultorgid})
											.trigger('reloadGrid');
						}
					} else {
						$('#list10_d').jqGrid('setGridParam', {url: 'places/getsub?placesid=' + ids + '&orgid=' + defaultorgid});
						$('#list10_d').jqGrid('setGridParam', {editurl: 'places/changesub?placesid=' + ids + '&orgid=' + defaultorgid})
										.trigger('reloadGrid');
					}
				}
			});
			$('#list2').jqGrid('setGridHeight', $(window).innerHeight() / 2);
			var addOptions = {
				top: 0, left: 0, width: 500
			};
			$('#list2').jqGrid('navGrid', '#pager2', {edit: false, add: true, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});
		}

		function GetSubGrid() {
			var addOptions = {
				top: 0, left: 0, width: 500
			};
			$('#list10_d').jqGrid({
				height: 100,
				autowidth: true,
				url: 'places/getsub',
				datatype: 'json',
				colNames: ['Id', 'Сотрудник', 'Действия'],
				colModel: [
					{name: 'places_users.id', index: 'places_users.id', width: 10, hidden: true},
					{name: 'name', index: 'name', width: 200, editable: true, edittype: 'select', editoptions: {
							editrules: {required: true},
							dataUrl: 'route/controller/server/common/getlistusers.php?orgid=' + defaultorgid
						}},
					{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
				],
				rowNum: 5,
				pager: '#pager10_d',
				sortname: 'places_users.id',
				scroll: 1,
				viewrecords: true,
				sortorder: 'asc',
				caption: 'Рабочие места'
			}).navGrid('#pager10_d', {add: true, edit: false, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});
		}

		GetGrid();
		GetSubGrid();
	</script>

<?php endif;
