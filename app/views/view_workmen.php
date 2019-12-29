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
 * Инструменты / Менеджер по обслуживанию
 */

# Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$user = User::getInstance();
$cfg = Config::getInstance();

# Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1,3,4,5,6')):
?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Инструменты / Менеджер по обслуживанию"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ", "Просмотр", "Добавление", "Редактирование", "Удаление".
	</div>

	<?php
	die();
endif;
?>

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
<script>
	function exportExcel(list, tmc) {
		$().toastmessage('showWarningToast', 'Экспортирование не реализовано!');
	}

	$('#workmen').jqGrid({
		url: 'route/controller/server/tmc/workmen.php',
		datatype: 'json',
		colNames: ['Статус', 'Организация', 'Помещение', 'Группа', 'Id', 'Инв.№', 'ТМЦ', 'Ответственный', 'За месяц', 'За год'],
		colModel: [
			{name: 'repair', index: 'repair', width: 100, search: false},
			{name: 'orgname', index: 'orgname', width: 155, stype: 'select',
				searchoptions: {dataUrl: 'route/controller/server/common/getlistorgs.php?addnone=true'}},
			{name: 'placename', index: 'placename', width: 150, search: false},
			{name: 'groupnomename', index: 'groupnomename', width: 150, stype: 'select',
				searchoptions: {dataUrl: 'route/controller/server/equipment/getlistgroupname.php?addnone=true'}},
			{name: 'idnome', index: 'idnome', width: 50},
			{name: 'invnum', index: 'invnum', width: 100},
			{name: 'nomename', index: 'nomename', width: 200},
			{name: 'fio', index: 'fio', width: 200, search: false},
			{name: 'bymonth', index: 'bymonth', width: 50, search: false},
			{name: 'byear', index: 'byear', width: 50, search: false}
		],
		onSelectRow: function (ids) {
			$('#photoid').load('route/controller/server/equipment/getphoto.php?eqid=' + ids);
			$.jgrid.gridUnload('#tbl_rep');
			$('#tbl_rep').jqGrid('setGridParam', {url: 'route/controller/server/equipment/getrepinfo.php?eqid=' + ids});
			$('#tbl_rep').jqGrid({
				url: 'route/controller/server/equipment/getrepinfo.php?eqid=' + ids,
				datatype: "json",
				colNames: ['Id', 'Дата начала', 'Дата окончания', 'Организация', 'Стоимость', 'Комментарий', 'Статус', 'Отправитель', 'Получатель', 'Документ', ''],
				colModel: [
					{name: 'id', index: 'id', width: 25, editable: false, hidden: true},
					{name: 'dt', index: 'dt', width: 95, editable: true, sorttype: 'date', editoptions: {size: 20,
							dataInit: function (el) {
								vl = $(el).val();
								$(el).datepicker();
								$(el).datepicker('option', 'dateFormat', 'dd.mm.yy');
								$(el).datepicker('setDate', vl);
							}}
					},
					{name: 'dtend', index: 'dtend', width: 95, editable: true, editoptions: {size: 20,
							dataInit: function (el) {
								vl = $(el).val();
								$(el).datepicker();
								$(el).datepicker('option', 'dateFormat', 'dd.mm.yy');
								$(el).datepicker('setDate', vl);
							}}
					},
					{name: 'kntname', index: 'kntname', width: 120},
					{name: 'cost', index: 'cost', width: 80, editable: true, editoptions: {size: 20,
							dataInit: function (el) {
								$(el).focus();
							}}
					},
					{name: 'comment', index: 'comment', width: 200, editable: true},
					{name: 'status', index: 'status', width: 80, editable: true, edittype: 'select', editoptions: {value: '1:В сервисе;0:Работает;2:Есть заявка;3:Списать'}},
					{name: 'userfrom', index: 'userfrom', width: 200},
					{name: 'userto', index: 'userto', width: 200},
					{name: 'doc', index: 'doc', width: 200, editable: true},
					{name: 'myac', width: 60, fixed: true, sortable: false, resize: false, formatter: 'actions',
						formatoptions: {keys: true,
							afterSave: function (rowid) {
								$('#workmen').jqGrid().trigger('reloadGrid');
							}
						}}
				],
				autowidth: true,
				pager: '#rp_nav',
				sortname: 'id',
				scroll: 1,
				shrinkToFit: true,
				viewrecords: true,
				height: 200,
				sortorder: 'desc',
				editurl: 'route/controller/server/equipment/getrepinfo.php?eqid=' + ids,
				caption: 'История ремонтов'
			}).trigger('reloadGrid');
			$('#tbl_rep').jqGrid('navGrid', '#rp_nav', {edit: false, add: false, del: false, search: false});
			$('#tbl_rep').jqGrid('navButtonAdd', '#rp_nav', {
				caption: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
				title: 'Изменить статус ремонта',
				buttonicon: 'none',
				onClickButton: function () {
					var id = $('#tbl_rep').jqGrid('getGridParam', 'selrow');
					if (id) {
						$('#tbl_rep').jqGrid('getRowData', id);
						$('#pg_add_edit').dialog({autoOpen: false, height: 480, width: 620, modal: true, title: 'Ремонт имущества'});
						$('#pg_add_edit').dialog('open');
						$('#pg_add_edit').load('route/controller/client/view/equipment/service.php?step=edit&eqid=' + id);
					} else {
						$().toastmessage('showWarningToast', 'Выберите ТМЦ для изменения статуса ремонта!');
					}
				}
			});
			$('#tbl_rep').jqGrid('navButtonAdd', '#rp_nav', {
				caption: '<i class="fa fa-floppy-o" aria-hidden="true"></i>',
				title: 'Экспорт в Excel',
				buttonicon: 'none',
				onClickButton: function () {
					var id = $('#workmen').jqGrid('getGridParam', 'selrow');
					if (id) { // если выбрана строка ТМЦ который уже в ремонте, открываем список с фильтром по этому ТМЦ
						var ret = $('#workmen').jqGrid('getRowData', id);
						tmc = ret.nomename + ' инвентарный №' + ret.invnum;
						exportExcel('#tbl_rep', tmc);
					} else {
						$().toastmessage('showWarningToast', 'Выберите ТМЦ для вывода отчета!');
					}
				}
			});
		},
		autowidth: true,
		shrinkToFit: true,
		height: 200,
		grouping: true,
		groupingView: {
			groupText: ['<b>{0} - {1} Item(s)</b>'],
			groupCollapse: false,
			groupField: ['repair']
		},
		pager: '#workmen_footer',
		sortname: 'orgname',
		viewrecords: true,
		rowNum: 1000,
		scroll: 1,
		sortorder: 'asc',
		editurl: 'route/controller/server/tmc/workmen.php',
		caption: 'Сервисное обслуживание ТМЦ'
	});
	$('#workmen').jqGrid('navGrid', '#workmen_footer', {edit: false, add: false, del: false, search: false});
	$('#workmen').jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});

	$('#workmen').jqGrid('navButtonAdd', '#workmen_footer', {
		caption: '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>',
		title: 'Отдать в ремонт ТМЦ',
		buttonicon: 'none',
		onClickButton: function () {
			var id = $('#workmen').jqGrid('getGridParam', 'selrow');
			if (id) { // если выбрана строка ТМЦ который уже в ремонте, открываем список с фильтром по этому ТМЦ
				$('#workmen').jqGrid('getRowData', id);
				$('#pg_add_edit').dialog({autoOpen: false, height: 480, width: 620, modal: true, title: 'Ремонт имущества'});
				$('#pg_add_edit').dialog('open');
				$('#pg_add_edit').load('route/controller/client/view/equipment/service.php?step=add&eqid=' + id);
			} else {
				$().toastmessage('showWarningToast', 'Выберите ТМЦ для ремонта!');
			}
		}
	});
</script>
