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

// Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

use core\request;

$req = request::getInstance();
$id = $req->get('id');
?>
<table id="list_rep"></table>
<div id="pager_rep"></div>
<div id="comment_rep"></div>
<script>repid = "<?= $id; ?>";</script>
<script>
	var lastsel3;

	$('#list_rep').jqGrid({
		url: 'route/deprecated/server/equipment/repair.php?step=list&id=' + repid + '&eqid=' + repid,
		datatype: 'json',
		colNames: ['Id', 'Контрагент', 'Оргтехника', 'Дата начала', 'Дата конца', 'Стоимость', 'Комментарий', 'Статус'],
		colModel: [
			{name: 'rpid', index: 'rpid', width: 35},
			{name: 'namekont', index: 'namekont', width: 100, editable: false},
			{name: 'namenome', index: 'namenome', width: 100, editable: false},
			{name: 'dt', index: 'dt', width: 80, editable: true, sorttype: "date"},
			{name: 'dtend', index: 'dtend', width: 80, editable: true, sorttype: "date"},
			{name: 'cost', index: 'cost', width: 80, editable: true},
			{name: 'comment', index: 'comment', width: 80, editable: true},
			{name: 'rstatus', index: 'rstatus', width: 100, editable: true, edittype: "select", editoptions: {value: '1:Ремонт;0:Сделано'}}
		],
		onSelectRow: function (id) {
			$('#comment_rep').html($('#' + id + '_comment').val());
			if (id && id !== lastsel3) {
				$('#list_rep').jqGrid('restoreRow', lastsel3);
				$('#list_rep').jqGrid('editRow', id, true, pickdates);
				lastsel3 = id;
			}
		},
		autowidth: true,
		rowNum: 10,
		rowList: [10, 20, 30],
		pager: '#pager_rep',
		sortname: 'rpid',
		viewrecords: true,
		sortorder: 'asc',
		editurl: 'route/deprecated/server/equipment/repair.php?step=edit',
		caption: 'Реестр ремонтов'
	});

	function pickdates(id) {
		$('#' + id + '_dt', '#list_rep').datepicker({dateFormat: 'dd.mm.yy'});
		$('#' + id + '_dtend', '#list_rep').datepicker({dateFormat: 'dd.mm.yy'});
		$('#comment_rep').html($('#' + id + '_comment').val());
	}
</script>
