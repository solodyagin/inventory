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
 * Инструменты / Контроль договоров
 */

$user = User::getInstance();
$cfg = Config::getInstance();

/* Проверка: если пользователь - не администратор и не назначена одна из ролей, то */
if (!$user->isAdmin() && !$user->TestRights([1,3,4,5,6])):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Инструменты / Контроль договоров"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ", "Просмотр", "Добавление", "Редактирование", "Удаление".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<h4>Контроль договоров</h4>
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="list2"></table>
				<div id="pager2"></div>
				<div id="info_contract"></div>
			</div>
		</div>
	</div>
	<script>
		$.extend($.jgrid.defaults, {ajaxSelectOptions: {cache: false}});

		$('#list2').jqGrid({
			url: 'route/deprecated/server/knt/libre_knt.php?org_status=list',
			datatype: 'json',
			colNames: [' ', 'Id', 'Имя', 'ИНН', 'КПП', 'Пок', 'Прод', 'К.договор', 'ERPCode', 'Комментарий', 'Действия'],
			colModel: [
				{name: 'active', index: 'active', width: 20, search: false, hidden: true},
				{name: 'id', index: 'id', width: 55, search: false, hidden: true},
				{name: 'name', index: 'name', width: 200, editable: true},
				{name: 'INN', index: 'INN', width: 100, editable: true},
				{name: 'KPP', index: 'KPP', width: 100, editable: true},
				{name: 'bayer', index: 'bayer', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: 'Yes:No'}, search: false},
				{name: 'supplier', index: 'supplier', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: 'Yes:No'}, search: false},
				{name: 'dog', index: 'dog', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: 'Yes:No'}, search: false},
				{name: 'ERPCode', index: 'ERPCode', width: 100, editable: true, search: false, hidden: true},
				{name: 'comment', index: 'comment', width: 200, editable: true},
				{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}, search: false, hidden: true}
			],
			autowidth: true,
			rowNum: 20,
			rowList: [20, 40, 60],
			pager: '#pager2',
			sortname: 'id',
			scroll: 1,
			viewrecords: true,
			sortorder: 'asc',
			editurl: 'route/deprecated/server/knt/libre_knt.php?org_status=edit',
			caption: 'Справочник контрагентов',
			onSelectRow: function (ids) {
				$('#info_contract').load('route/deprecated/server/knt/info_contract.php?kntid=' + ids);
			}
		});

		$('#list2').jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});
	</script>

<?php endif;
