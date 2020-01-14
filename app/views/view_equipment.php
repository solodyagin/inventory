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
 * Журналы / Имущество
 */

$user = User::getInstance();
$cfg = Config::getInstance();

# Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1,3,4,5,6')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Журналы / Имущество"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ", "Просмотр", "Добавление", "Редактирование", "Удаление".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<div class="row">
			<div class="col-md-3 col-sm-3">
				<select class="chosen-select form-control" name="orgs" id="orgs">
					<?php
					$morgs = GetArrayOrgs(); # список активных организаций
					for ($i = 0; $i < count($morgs); $i++) {
						$idorg = $morgs[$i]['id'];
						$nameorg = $morgs[$i]['name'];
						$sl = ($idorg == $cfg->defaultorgid) ? 'selected' : '';
						echo "<option value=\"$idorg\" $sl>$nameorg</option>";
					}
					?>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="tbl_equpment"></table>
				<div id="pg_nav"></div>
				<div id="pg_add_edit"></div>
				<div class="row-fluid">
					<div class="col-xs-2 col-md-2 col-sm-2">
						<div id="photoid"></div>
					</div>
					<div class="col-xs-10 col-md-10 col-sm-10">
						<table id="tbl_move"></table>
						<div id="mv_nav"></div>
						<table id="tbl_rep"></table>
						<div id="rp_nav"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		$('#orgs').change(function () {
			var exdate = new Date();
			exdate.setDate(exdate.getDate() + 365);
			orgid = $('#orgs :selected').val();
			defaultorgid = orgid;
			document.cookie = 'defaultorgid=' + orgid + '; path=/; expires=' + exdate.toUTCString();
			$.jgrid.gridUnload('#tbl_equpment');
			LoadTable();
		});

		function LoadTable() {
			var table = $('#tbl_equpment');
			table.jqGrid({
				url: 'route/deprecated/server/equipment/equipment.php?sorgider=' + defaultorgid,
				datatype: 'json',
				colNames: [' ', 'Id', 'IP', 'Помещение', 'Номенклатура', 'Группа', 'В пути',
					'Производитель', 'Имя по бухгалтерии', 'Сер.№', 'Инв.№',
					'Штрихкод', 'Организация', 'Мат.отв.', 'Оприходовано', 'Стоимость',
					'Тек. стоимость', 'ОС', 'Списано', 'Карта', 'Комментарий', 'Ремонт',
					'Гар.срок', 'Поставщик', 'Действия'],
				colModel: [
					{name: 'active', index: 'active', width: 20, search: false, frozen: true, fixed: true},
					{name: 'equipment.id', index: 'equipment.id', width: 55, search: false, frozen: true, hidden: true, fixed: true},
					{name: 'ip', index: 'ip', width: 100, hidden: true, fixed: true},
					{name: 'placesid', index: 'placesid', width: 155, stype: 'select', frozen: true, fixed: true,
						searchoptions: {dataUrl: 'route/deprecated/server/equipment/getlistplaces.php?addnone=true'}},
					{name: 'nomename', index: 'getvendorandgroup.nomename', width: 135, frozen: true},
					{name: 'getvendorandgroup.groupname', index: 'getvendorandgroup.grnomeid', width: 100, stype: 'select', fixed: true,
						searchoptions: {dataUrl: 'route/deprecated/server/equipment/getlistgroupname.php?addnone=true'}},
					{name: 'tmcgo', index: 'tmcgo', width: 80, search: true, stype: 'select', fixed: true,
						searchoptions: {dataUrl: 'route/deprecated/server/equipment/getlisttmcgo.php?addnone=true'},
						formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: 'Yes:No'}, editable: true, hiddem: true
					},
					{name: 'getvendorandgroup.vendorname', index: 'getvendorandgroup.vendorname', width: 100},
					{name: 'buhname', index: 'buhname', width: 155, editable: true, hidden: true},
					{name: 'sernum', index: 'sernum', width: 100, editable: true, fixed: true},
					{name: 'invnum', index: 'invnum', width: 100, editable: true, fixed: true},
					{name: 'shtrihkod', index: 'shtrihkod', width: 100, editable: true, hidden: true, fixed: true},
					{name: 'org.name', index: 'org.name', width: 155, hidden: true},
					{name: 'fio', index: 'fio', width: 100},
					{name: 'datepost', index: 'datepost', width: 80, fixed: true},
					{name: 'cost', index: 'cost', width: 55, editable: true, hidden: true, fixed: true},
					{name: 'currentcost', index: 'currentcost', width: 55, editable: true, hidden: true, fixed: true},
					{name: 'os', index: 'os', width: 35, editable: true, formatter: 'checkbox', edittype: 'checkbox', fixed: true,
						editoptions: {value: 'Yes:No'}, search: false, hidden: true},
					{name: 'mode', index: 'equipment.mode', width: 55, editable: true, formatter: 'checkbox', edittype: 'checkbox', fixed: true,
						editoptions: {value: 'Yes:No'}, search: false, hidden: true},
					{name: 'eqmapyet', index: 'eqmapyet', width: 55, editable: true, formatter: 'checkbox', edittype: 'checkbox', fixed: true,
						editoptions: {value: 'Yes:No'}, search: false, hidden: true},
					{name: 'comment', index: 'equipment.comment', width: 200, editable: true, edittype: 'textarea',
						editoptions: {rows: '3', cols: '10'}, search: false, hidden: true},
					{name: 'eqrepair', hidden: true, index: 'eqrepair', width: 35, editable: true, formatter: 'checkbox', edittype: 'checkbox',
						editoptions: {value: 'Yes:No'}, search: false},
					{name: 'dtendgar', index: 'dtendgar', width: 55, editable: false, hidden: true, search: false, fixed: true},
					{name: 'kntname', index: 'kntname', width: 55, editable: false, hidden: true, search: false},
					{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions',
						formatoptions: {keys: true}, search: false}
				],
				gridComplete: function () {
					table.loadCommonParam('tbleq');
				},
				resizeStop: function () {
					table.saveCommonParam('tbleq');
				},
				onSelectRow: function (ids) {
					$('#photoid').load('route/deprecated/server/equipment/getphoto.php?eqid=' + ids);
					$('#tbl_move').jqGrid('setGridParam', {url: 'route/deprecated/server/equipment/getmoveinfo.php?eqid=' + ids});
					$('#tbl_move').jqGrid({
						url: 'route/deprecated/server/equipment/getmoveinfo.php?eqid=' + ids,
						datatype: 'json',
						colNames: ['Id', 'Дата', 'Организация', 'Помещение',
							'Сотрудник', 'Организация', 'Помещение', 'Сотрудник', '',
							'Комментарий', ''],
						colModel: [
							{name: 'id', index: 'id', width: 25, hidden: true},
							{name: 'dt', index: 'dt', width: 95},
							{name: 'orgname1', index: 'orgname1', width: 120, hidden: true},
							{name: 'place1', index: 'place1', width: 80},
							{name: 'user1', index: 'user1', width: 90},
							{name: 'orgname2', index: 'orgname2', width: 120, hidden: true},
							{name: 'place2', index: 'place2', width: 80},
							{name: 'user2', index: 'user2', width: 90},
							{name: 'name', index: 'name', width: 90, hidden: true},
							{name: 'comment', index: 'comment', width: 200, editable: true},
							{name: 'myac', width: 60, fixed: true, sortable: false, resize: false,
								formatter: 'actions', formatoptions: {keys: true}}
						],
						autowidth: true,
						pager: '#mv_nav',
						sortname: 'dt',
						scroll: 1,
						shrinkToFit: true,
						viewrecords: true,
						height: 200,
						sortorder: 'asc',
						editurl: 'route/deprecated/server/equipment/getmoveinfo.php?eqid=' + ids,
						caption: 'История перемещений'
					}).trigger('reloadGrid');
					$('#tbl_move').jqGrid('destroyGroupHeader');
					$('#tbl_move').jqGrid('setGroupHeaders', {
						useColSpanStyle: true,
						groupHeaders: [
							{startColumnName: 'orgname1', numberOfColumns: 3, titleText: 'Откуда'},
							{startColumnName: 'orgname2', numberOfColumns: 3, titleText: 'Куда'}
						]
					});
					$.jgrid.gridUnload('#tbl_rep');
					$('#tbl_rep').jqGrid('setGridParam', {url: 'route/deprecated/server/equipment/getrepinfo.php?eqid=' + ids});
					$('#tbl_rep').jqGrid({
						url: 'route/deprecated/server/equipment/getrepinfo.php?eqid=' + ids,
						datatype: 'json',
						colNames: ['Id', 'Дата начала', 'Дата окончания', 'Организация', 'Стоимость', 'Комментарий', 'Статус', ''],
						colModel: [
							{name: 'id', index: 'id', width: 25, editable: false},
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
							{name: 'status', index: 'status', width: 80, editable: true, edittype: 'select',
								editoptions: {value: '1:Ремонт;0:Сделано'}},
							{name: 'myac', width: 60, fixed: true, sortable: false, resize: false, formatter: 'actions',
								formatoptions: {keys: true,
									afterSave: function () {
										table.jqGrid().trigger('reloadGrid');
									}
								}}
						],
						autowidth: true,
						pager: '#rp_nav',
						sortname: 'dt',
						scroll: 1,
						viewrecords: true,
						height: 200,
						sortorder: 'asc',
						editurl: 'route/deprecated/server/equipment/getrepinfo.php?eqid=' + ids,
						caption: 'История ремонтов'
					}).trigger('reloadGrid');
					$('#tbl_rep').jqGrid('navGrid', '#rp_nav', {edit: false, add: false, del: false, search: false});
					$('#tbl_rep').jqGrid('navButtonAdd', '#rp_nav', {
						caption: '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>',
						title: 'Отдать в ремонт',
						buttonicon: 'none',
						onClickButton: function () {
							var id = table.jqGrid('getGridParam', 'selrow');
							if (id) { // если выбрана строка ТМЦ который уже в ремонте, открываем список с фильтром по этому ТМЦ
								table.jqGrid('getRowData', id);
								$('#pg_add_edit').dialog({autoOpen: false, height: 380, width: 620, modal: true, title: 'Ремонт имущества'});
								$('#pg_add_edit').dialog('open');
								$('#pg_add_edit').load('route/deprecated/client/view/equipment/repair.php?step=add&eqid=' + id);
							} else {
								$().toastmessage('showWarningToast', 'Выберите оргтехнику для ремонта!');
							}
						}
					});
				},
				subGridRowExpanded: function (subgrid_id, row_id) {
					// we pass two parameters
					// subgrid_id is a id of the div tag created whitin a table data
					// the id of this elemenet is a combination of the "sg_" + id of the row
					// the row_id is the id of the row
					// If we wan to pass additinal parameters to the url we can use
					// a method getRowData(row_id) - which returns associative array in type name-value
					// here we can easy construct the flowing
					var subgrid_table_id, pager_id;
					subgrid_table_id = subgrid_id + '_t';
					pager_id = 'p_' + subgrid_table_id;
					$('#' + subgrid_id).html('<table border="1" id="' + subgrid_table_id + '" class="scroll"></table><div id="' + pager_id + '" class="scroll"></div>');
					$('#' + subgrid_table_id).jqGrid({
						url: 'route/deprecated/server/equipment/paramlist.php?eqid=' + row_id,
						datatype: 'json',
						colNames: ['Id', 'Наименование', 'Параметр', ''],
						colModel: [
							{name: 'id', index: 'num', width: 60, key: true},
							{name: 'name', index: 'item', width: 150},
							{name: 'param', index: 'qty', width: 310, editable: true},
							{name: 'myac', width: 80, fixed: true, sortable: false, resize: false,
								formatter: 'actions', formatoptions: {keys: true}}
						],
						editurl: 'route/deprecated/server/equipment/paramlist.php?eqid=' + row_id,
						pager: pager_id,
						sortname: 'name',
						sortorder: 'asc',
						scroll: 1,
						height: 'auto'
					});
				},
				subGridRowColapsed: function (subgrid_id, row_id) {
					// this function is called before removing the data
					var subgrid_table_id;
					subgrid_table_id = subgrid_id + '_t';
					$('#' + subgrid_table_id).remove();
				},
				subGrid: true,
				multiselect: true,
				autowidth: true,
				shrinkToFit: true,
				pager: '#pg_nav',
				sortname: 'equipment.id',
				rowNum: 40,
				viewrecords: true,
				sortorder: 'asc',
				editurl: 'route/deprecated/server/equipment/equipment.php?sorgider=' + defaultorgid,
				caption: 'Оргтехника'
			});
			table.jqGrid('setGridHeight', $(window).innerHeight() /*- 285*/ / 2);
			table.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});
			table.jqGrid('bindKeys', '');
			table.jqGrid('navGrid', '#pg_nav', {edit: false, add: false, del: false, search: false});
			table.jqGrid('setFrozenColumns');
			table.jqGrid('navButtonAdd', '#pg_nav', {
				caption: '<i class="fa fa-tag" aria-hidden="true"></i>',
				title: 'Выбор колонок',
				buttonicon: 'none',
				onClickButton: function () {
					table.jqGrid('columnChooser', {
						done: function () {
							table.saveCommonParam('tbleq');
						},
						width: 550,
						dialog_opts: {
							modal: true,
							minWidth: 470,
							height: 470
						},
						msel_opts: {
							dividerLocation: 0.5
						}
					});
				}
			});
			table.jqGrid('navButtonAdd', '#pg_nav', {
				caption: '<i class="fa fa-plus-circle" aria-hidden="true"></i>',
				title: 'Добавить',
				buttonicon: 'none',
				onClickButton: function () {
					$('#pg_add_edit').dialog({autoOpen: false, height: 600, width: 780, modal: true, title: 'Добавление имущества'});
					$('#pg_add_edit').dialog('open');
					$('#pg_add_edit').load('route/deprecated/client/view/equipment/equipment.php?step=add&id=');
				}
			});
			table.jqGrid('navButtonAdd', '#pg_nav', {
				caption: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
				title: 'Редактировать',
				buttonicon: 'none',
				onClickButton: function () {
					var gsr = table.jqGrid('getGridParam', 'selrow');
					if (gsr) {
						$('#pg_add_edit').dialog({autoOpen: false, height: 600, width: 780, modal: true, title: 'Редактирование имущества'});
						$('#pg_add_edit').dialog('open');
						$('#pg_add_edit').load('route/deprecated/client/view/equipment/equipment.php?step=edit&id=' + gsr);
					} else {
						$().toastmessage('showWarningToast', 'Сначала выберите строку!');
					}
				}
			});
			table.jqGrid('navButtonAdd', '#pg_nav', {
				caption: '<i class="fa fa-arrows" aria-hidden="true"></i>',
				title: 'Переместить',
				buttonicon: 'none',
				onClickButton: function () {
					var gsr = table.jqGrid('getGridParam', 'selrow');
					if (gsr) {
						$('#pg_add_edit').dialog({
							height: 440,
							width: 620,
							modal: true,
							title: 'Перемещение имущества',
							open: function () {
								$(this).load('route/deprecated/client/view/equipment/move.php?id=' + gsr);
							}
						});
					} else {
						$().toastmessage('showWarningToast', 'Сначала выберите строку!');
					}
				}
			});
			table.jqGrid('navButtonAdd', '#pg_nav', {
				caption: '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>',
				title: 'Отдать в ремонт',
				buttonicon: 'none',
				onClickButton: function () {
					var id = table.jqGrid('getGridParam', 'selrow');
					if (id) { // если выбрана строка ТМЦ который уже в ремонте, открываем список с фильтром по этому ТМЦ
						table.jqGrid('getRowData', id);
						$('#pg_add_edit').dialog({autoOpen: false, height: 380, width: 620, modal: true, title: 'Ремонт имущества'});
						$('#pg_add_edit').dialog('open');
						$('#pg_add_edit').load('route/deprecated/client/view/equipment/repair.php?step=add&eqid=' + id);
					} else {
						$().toastmessage('showWarningToast', 'Сначала выберите строку!');
					}
				}
			});
			table.jqGrid('navButtonAdd', '#pg_nav', {
				caption: '<i class="fa fa-table" aria-hidden="true"></i>',
				title: 'Вывести штрихкоды',
				buttonicon: 'none',
				onClickButton: function () {
					var gsr = table.jqGrid('getGridParam', 'selrow');
					if (gsr) {
						var s;
						s = table.jqGrid('getGridParam', 'selarrrow');
						newWin = window.open('route/inc/ean13print.php?mass=' + s, 'printWindow');
					} else {
						$().toastmessage('showWarningToast', 'Сначала выберите строку!');
					}
				}
			});
			table.jqGrid('navButtonAdd', '#pg_nav', {
				caption: '<i class="fa fa-book" aria-hidden="true"></i>',
				title: 'Отчеты',
				buttonicon: 'none',
				onClickButton: function () {
					newWin2 = window.open('report', 'printWindow2');
				}
			});
			table.jqGrid('navButtonAdd', '#pg_nav', {
				caption: '<i class="fa fa-floppy-o" aria-hidden="true"></i>',
				title: 'Экспорт XML',
				buttonicon: 'none',
				onClickButton: function () {
					newWin2 = window.open('route/deprecated/server/equipment/export_xml.php', 'printWindow4');
				}
			});
			table.jqGrid('setFrozenColumns');
		}

		function GetListUsers(orgid, userid) {
			$('#susers').load('route/deprecated/server/getlistusers.php?orgid=' + orgid + '&userid=' + userid);
		}

		function GetListPlaces(orgid, placesid) {
			$('#splaces').load('route/deprecated/server/getlistplaces.php?orgid=' + orgid + '&placesid=' + placesid);
		}

		$(function () {
			for (var selector in config) {
				$(selector).chosen(config[selector]);
			}
			LoadTable();
		});
	</script>

<?php endif;
