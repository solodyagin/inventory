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

namespace app\views;

use core\config;
use core\user;
use core\utils;

$user = user::getInstance();
$cfg = config::getInstance();
?>
<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="row-fluid">
		<form class="form-horizontal" enctype="multipart/form-data" action="?content_page=reports&step=view" method="post" name="form1" target="_self">
			<div class="form-group">
				<div class="col-xs-12 col-md-4 col-sm-4">
					<label for="sel_rep" class="control-label">Название отчета</label>
					<select class="chosen-select" name="sel_rep" id="sel_rep">
						<option value="1">Наличие оргтехники</option>
						<option value="2">Наличие оргтехники - только не ОС и не списанное</option>
					</select>
					<label for="sel_plp" class="control-label">Сотрудник</label>
					<div name="sel_plp" id="sel_plp"></div>
				</div>
				<div class="col-xs-12 col-md-4 col-sm-4">
					<label for="sel_orgid" class="control-label">Организация</label>
					<select class="chosen-select" name="sel_orgid" id="sel_orgid">
						<?php
						$morgs = utils::getArrayOrgs();
						for ($i = 0; $i < count($morgs); $i++) {
							$nid = $morgs[$i]['id'];
							$sl = ($nid == $user->orgid) ? 'selected' : '';
							echo "<option value=\"$nid\" $sl>{$morgs[$i]['name']}</option>";
						}
						?>
					</select>
					<div class="checkbox">
						<label class="checkbox">
							<input type="checkbox" name="os" id="os" value="1"> Основные
						</label>
						<label class="checkbox">
							<input type="checkbox" name="mode" id="mode" value="1"> Списано
						</label>
						<label class="checkbox">
							<input type="checkbox" name="gr" id="gr" value="1"> По группам
						</label>
					</div>
				</div>
				<div class="col-xs-12 col-md-4 col-sm-4">
					<label for="sel_pom" class="control-label">Помещение</label>
					<div name="sel_pom" id="sel_pom"></div>
					<div class="checkbox">
						<label class="checkbox">
							<input type="checkbox" name="repair" id="repair" value="1"> В ремонте
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-4">
					<button class="btn btn-primary" id="sbt">Сформировать</button>
					<button class="btn btn-default" id="btprint">Распечатать</button>
				</div>
			</div>
		</form>
		<table id="list2"></table>
		<div id="pager2"></div>
	</div>
</div>
<script>curuserid = <?= $user->id; ?>;</script>
<script>
	function listEqByPlaces(oid, pid, plpid) {
		if (!$('#gr').prop('checked')) {
			$('#list2').jqGrid({
				url: 'report/list?curuserid=' + plpid + '&curorgid=' + oid + '&curplid=' + pid + '&tpo=' + $('#sel_rep :selected').val() + '&os=' + $('#os').prop('checked') + '&mode=' + $('#mode').prop('checked') + '&repair=' + $('#repair').prop('checked'),
				datatype: 'json',
				colNames: ['Id', 'Помещение', 'Наименование', 'Группа', 'Инвентарник', 'Серийник', 'Штрихкод', 'Списан', 'ОС', 'Бух.имя'],
				colModel: [
					{name: 'id', index: 'id', width: 20, hidden: true},
					{name: 'plname', index: 'plname', width: 110},
					{name: 'namenome', index: 'namenome', width: 140},
					{name: 'grname', index: 'grname', width: 140},
					{name: 'invnum', index: 'invnum', width: 100},
					{name: 'sernum', index: 'sernum', width: 100},
					{name: 'shtrihkod', index: 'shtrihkod', width: 100},
					{name: 'mode', index: 'mode', width: 55, formatter: 'checkbox', edittype: 'checkbox'},
					{name: 'os', index: 'os', width: 55, formatter: 'checkbox', edittype: 'checkbox'},
					{name: 'buhname', index: 'buhname', width: 155}
				],
				rownumbers: true,
				autowidth: true,
				height: 'auto',
				pager: '#pager2',
				sortname: 'plname',
				viewrecords: true,
				rowNum: 1000,
				scroll: 1,
				sortorder: 'asc',
				caption: 'Список имущества',
				multiselect: true
			});
		} else {
			$('#list2').jqGrid({
				url: 'report/list?curuserid=' + plpid + '&curorgid=' + oid + '&curplid=' + pid + '&tpo=' + $('#sel_rep :selected').val() + '&os=' + $('#os').prop('checked') + '&mode=' + $('#mode').prop('checked') + '&repair=' + $('#repair').prop('checked'),
				datatype: 'json',
				colNames: ['Id', 'Помещение', 'Наименование', 'Группа', 'Инвентарник', 'Серийник', 'Штрихкод', 'Списан', 'ОС', 'Бух.имя'],
				colModel: [
					{name: 'id', index: 'id', width: 20, hidden: true},
					{name: 'plname', index: 'plname', width: 110},
					{name: 'namenome', index: 'namenome', width: 140},
					{name: 'grname', index: 'grname', width: 140},
					{name: 'invnum', index: 'invnum', width: 100},
					{name: 'sernum', index: 'sernum', width: 100},
					{name: 'shtrihkod', index: 'shtrihkod', width: 100},
					{name: 'mode', index: 'mode', width: 55, formatter: 'checkbox', edittype: 'checkbox'},
					{name: 'os', index: 'os', width: 55, formatter: 'checkbox', edittype: 'checkbox'},
					{name: 'buhname', index: 'buhname', width: 155}
				],
				grouping: true,
				groupingView: {
					groupText: ['<b>{0} - {1} Item(s)</b>'],
					groupColumnShow: [false],
					groupField: ['grname']
				},
				rownumbers: true,
				autowidth: true,
				height: 'auto',
				pager: '#pager2',
				sortname: 'plname',
				viewrecords: true,
				rowNum: 1000,
				scroll: 1,
				sortorder: 'asc',
				caption: 'Список имущества'
			});
		}

		$('#list2').jqGrid('navGrid', '#pager2', {edit: false, add: false, del: false, search: false});

		$('#list2').jqGrid('navButtonAdd', '#pager2', {
			id: 'ExportToCSV',
			caption: '<i class="fas fa-save"></i>',
			title: 'Экспорт в CSV',
			buttonicon: 'none',
			onClickButton: function (e) {
				var gsr = $('#list2').jqGrid('getGridParam', 'selrow');
				if (gsr) {
					exportData(e, '#list2');
				} else {
					$().toastmessage('showWarningToast', 'Сначала выберите строки!');
				}
			}
		});
	}

	function exportData(e, id) {
		//var gridid = $(id).getDataIDs();
		//var label = $(id).getRowData(gridid[0]);
		var selRowIds = $(id).jqGrid('getGridParam', 'selarrrow');
		var obj = new Object();
		obj.count = selRowIds.length;
		if (obj.count) {
			obj.items = new Array();
			for (elem in selRowIds) {
				obj.items.push($(id).getRowData(selRowIds[elem]));
			}
			var json = JSON.stringify(obj);
			JSONToCSVConvertor(json, 'csv', 1);
		}
	}

	function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
		var arrData = (typeof JSONData !== 'object') ? JSON.parse(JSONData) : JSONData;
		var CSV = '';
		if (ShowLabel) {
			var row = '';
			for (var index in arrData.items[0]) {
				row += index + ';';
			}
			row = row.slice(0, -1);
			CSV += row + '\r\n';
		}
		for (var i = 0; i < arrData.items.length; i++) {
			var row = '';
			for (var index in arrData.items[i]) {
				row += '"' + arrData.items[i][index].replace(/(<([^>]+)>)/ig, '') + '";';
			}
			row.slice(0, row.length - 1);
			CSV += row + '\r\n';
		}
		if (CSV === '') {
			alert('Invalid data');
			return;
		}
		var link = document.createElement('a');
		link.id = 'lnkDwnldLnk';
		document.body.appendChild(link);
		var csv = CSV;
		blob = new Blob([csv], {type: 'text/csv'});
		var myURL = window.URL || window.webkitURL;
		$('#lnkDwnldLnk').attr({
			'download': 'Export.csv',
			'href': myURL.createObjectURL(blob)
		});
		$('#lnkDwnldLnk')[0].click();
		document.body.removeChild(link);
	}

	function updateChosen() {
		for (var selector in config) {
			$(selector).chosen({width: '100%'});
			$(selector).chosen(config[selector]);
		}
	}

	function getListPlaces(orgid, placesid) {
		url = 'route/deprecated/server/common/getlistplaces.php?orgid=' + orgid + '&placesid=' + placesid + '&addnone=true';
		$.get(url, function (data) {
			$('#sel_pom').html(data);
			updateChosen();
		});
	}

	function getListUsers(orgid, userid) {
		url = 'route/deprecated/server/common/getlistusers.php?orgid=' + orgid + '&userid=' + userid + '&addnone=true';
		$.get(url, function (data) {
			$('#sel_plp').html(data);
			updateChosen();
		});
	}

	$('#sel_orgid').change(function () {
		getListUsers($('#sel_orgid :selected').val());
		getListPlaces($('#sel_orgid :selected').val());
	});

	$('#sbt').click(function () {// обрабатываем отправку формы
		$.jgrid.gridUnload('#list2');
		listEqByPlaces($('#sel_orgid :selected').val(), $('#splaces :selected').val(), $('#suserid :selected').val());
		return false;
	});

	$('#btprint').click(function () {// обрабатываем отправку формы
		var newWin3 = window.open('', 'printWindow3', '');
		newWin3.focus();
		newWin3.document.write('<table id="list222">');
		newWin3.document.write($('#list2').html());
		newWin3.document.write('</table>');
	});

	getListUsers($('#sel_orgid :selected').val(), curuserid);
	getListPlaces($('#sel_orgid :selected').val(), curuserid);
</script>
