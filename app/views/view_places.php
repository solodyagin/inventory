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

/* Запрещаем прямой вызов скрипта. */
defined('SITE_EXEC') or die('Доступ запрещён');

/*
 * Справочники / Помещения
 */

$user = User::getInstance();
$cfg = Config::getInstance();

/* Проверка: если пользователь - не администратор и не назначена одна из ролей, то выводим сообщение */
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
			<div class="col-xs-12 col-md-6 col-sm-6">
				<table id="list1"></table>
				<div id="pager1"></div>
			</div>
			<div class="col-xs-12 col-md-6 col-sm-6">
				<table id="list2"></table>
				<div id="pager2"></div>
			</div>
		</div>
	</div>
	<script>
		$('#list1').jqGrid({
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
			pager: '#pager1',
			sortname: 'id',
			scroll: 1,
			viewrecords: true,
			sortorder: 'asc',
			editurl: 'places/change?orgid=' + defaultorgid,
			caption: 'Помещения',
			onSelectRow: function (ids) {
				if (ids == null) {
					ids = 0;
					if ($('#list2').jqGrid('getGridParam', 'records') > 0) {
						$('#list2').jqGrid('setGridParam', {url: 'places/getsub?placesid=' + ids + '&orgid=' + defaultorgid})
										.jqGrid('setGridParam', {editurl: 'places/changesub?placesid=' + ids + '&orgid=' + defaultorgid})
										.trigger('reloadGrid');
					}
				} else {
					$('#list2').jqGrid('setGridParam', {url: 'places/getsub?placesid=' + ids + '&orgid=' + defaultorgid})
									.jqGrid('setGridParam', {editurl: 'places/changesub?placesid=' + ids + '&orgid=' + defaultorgid})
									.trigger('reloadGrid');
				}
			}
		}).navGrid('#pager1', {add: true, edit: false, del: false, search: false}, {}, {}, {}, {multipleSearch: false}, {closeOnEscape: true})
						.jqGrid('setGridHeight', $(window).innerHeight() / 2);

		$('#list2').jqGrid({
			height: 100,
			autowidth: true,
			url: 'places/getsub',
			datatype: 'json',
			colNames: ['Id', 'Сотрудник', 'Действия'],
			colModel: [
				{name: 'places_users.id', index: 'places_users.id', width: 10, hidden: true},
				{name: 'name', index: 'name', width: 200, editable: true, edittype: 'select', editoptions: {
						editrules: {required: true},
						dataUrl: 'route/deprecated/server/common/getlistusers.php?orgid=' + defaultorgid
					}},
				{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
			],
			rowNum: 5,
			pager: '#pager2',
			sortname: 'places_users.id',
			scroll: 1,
			viewrecords: true,
			sortorder: 'asc',
			caption: 'Рабочие места'
		}).navGrid('#pager2', {add: true, edit: false, del: false, search: false}, {}, {}, {}, {multipleSearch: false}, {closeOnEscape: true})
						.jqGrid('setGridHeight', $(window).innerHeight() / 2);
	</script>

<?php endif;
