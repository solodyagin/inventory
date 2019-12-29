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
 * Настройка / Подключенные модули
 */

# Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$user = User::getInstance();
$cfg = Config::getInstance();

# Проверка: если не администратор и нет полных прав, то
if (!$user->isAdmin() && !$user->TestRoles('1')):
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
			</div>
		</div>
	</div>
	<script>
		$('#list2').jqGrid({
			url: 'modules/get',
			datatype: 'json',
			colNames: ['Id', 'Имя', 'Комментарий', 'Автор', 'Включено', 'Действия'],
			colModel: [
				{name: 'id', index: 'id', width: 10, editable: false, hidden: true},
				{name: 'name', index: 'name', width: 80, editable: false},
				{name: 'comment', index: 'comment', width: 100, editable: false},
				{name: 'copy', index: 'copy', width: 120, editable: false},
				{name: 'active', index: 'active', width: 30, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: '1:0'}},
				{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
			],
			autowidth: true,
			pager: '#pager2',
			sortname: 'name',
			rowNum: 30,
			viewrecords: true,
			sortorder: 'asc',
			editurl: 'modules/change',
			caption: 'Модули системы'
		});

		// загружаем навигационную панель
		$('#list2').jqGrid('navGrid', '#pager2', {edit: false, add: false, del: false, search: false});
		$('#list2').jqGrid('setGridHeight', $(window).innerHeight() / 2);
	</script>

<?php endif;
