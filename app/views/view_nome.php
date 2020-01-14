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
 * Справочники / Номенклатура
 */

$user = User::getInstance();
$cfg = Config::getInstance();

# Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1,4,5,6')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Справочники / Номенклатура"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ", "Добавление", "Редактирование", "Удаление".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="list2"></table>
				<div id="pager2"></div>
				<div id="add_edit"></div>
			</div>
		</div>
	</div>
	<script>
		$('#list2').jqGrid({
			url: 'route/deprecated/server/tmc/libre_nome.php',
			datatype: 'json',
			colNames: [' ', 'Id', 'Группа', 'Производитель', 'Наименование', ''],
			colModel: [
				{name: 'active', index: 'active', width: 20, search: false},
				{name: 'nomeid', index: 'nomeid', width: 55, hidden: true},
				{name: 'group_nome.name', index: 'group_nome.name', width: 200},
				{name: 'vendor.name', index: 'vendor.name', width: 200},
				{name: 'nomename', index: 'nome.name', width: 200, editable: true},
				{name: 'myac', width: 70, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}, search: false}
			],
			autowidth: true,
			height: 200,
			grouping: true,
			groupingView: {
				groupText: ['<b>{0} - {1} Item(s)</b>'],
				groupCollapse: true,
				groupField: ['group_nome.name']
			},
			pager: '#pager2',
			sortname: 'nomeid',
			viewrecords: true,
			rowNum: 1000,
			scroll: 1,
			sortorder: 'asc',
			editurl: 'route/deprecated/server/tmc/libre_nome.php',
			caption: 'Справочник номенклатуры'
		});
		$('#list2').jqGrid('setGridHeight', $(window).innerHeight() / 2);
		$('#list2').jqGrid('navGrid', '#pager2', {edit: false, add: false, del: false, search: false});
		$('#list2').jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});
		$('#list2').jqGrid('navButtonAdd', '#pager2', {
			buttonicon: 'none',
			caption: '<i class="fa fa-plus-circle" aria-hidden="true"></i>',
			onClickButton: function () {
				$('#add_edit').empty();
				$('#add_edit').dialog({
					title: 'Добавление номенклатуры',
					autoOpen: false,
					modal: true,
					height: 280,
					width: 640,
					open: function () {
						$(this).load('route/deprecated/client/view/tmc/nome_add_edit.php?step=add');
					}
				});
				$('#add_edit').dialog('open');
			}
		});
		$('#list2').jqGrid('navButtonAdd', '#pager2', {
			buttonicon: 'none',
			caption: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
			onClickButton: function () {
				var gsr = $('#list2').jqGrid('getGridParam', 'selrow');
				if (gsr) {
					$('#add_edit').empty();
					$('#add_edit').dialog({
						title: 'Редактирование номенклатуры',
						autoOpen: false,
						modal: true,
						height: 280,
						width: 640,
						open: function () {
							$(this).load('route/deprecated/client/view/tmc/nome_add_edit.php?step=edit&id=' + gsr);
						}
					});
					$('#add_edit').dialog('open');
				} else {
					$().toastmessage('showWarningToast', 'Сначала выберите строку!');
				}
			}
		});
	</script>

<?php endif;
