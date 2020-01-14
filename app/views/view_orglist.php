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
 * Справочники / Список организаций
 */

$user = User::getInstance();
$cfg = Config::getInstance();

# Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Справочники / Список организаций"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="o_list"></table>
				<div id="o_pager"></div>
			</div>
		</div>
	</div>
	<script>
		$('#o_list').jqGrid({
			url: 'orglist/list',
			datatype: 'json',
			colNames: [' ', 'Id', 'Имя организации', 'Действия'],
			colModel: [
				{name: 'active', index: 'active', width: 20},
				{name: 'id', index: 'id', width: 55},
				{name: 'name', index: 'name', width: 400, editable: true},
				{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
			],
			autowidth: true,
			pager: '#o_pager',
			sortname: 'id',
			scroll: 1,
			viewrecords: true,
			sortorder: 'asc',
			editurl: 'orglist/change',
			caption: 'Справочник организаций'
		});
		$('#o_list').jqGrid('setGridHeight', $(window).innerHeight() / 2);
		$('#o_list').jqGrid('navGrid', '#o_pager', {edit: false, add: true, del: false, search: false}, {}, {}, {}, {multipleSearch: false}, {closeOnEscape: true});
	</script>

<?php endif;
