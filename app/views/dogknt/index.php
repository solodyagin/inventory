<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="row">
		<div class="col-xs-12 col-md-12 col-sm-12">
			<table id="list1"></table>
			<div id="pager1"></div>
			<div id="info_contract"></div>
		</div>
	</div>
</div>
<script>
	$.extend($.jgrid.defaults, {ajaxSelectOptions: {cache: false}});

	var $list1 = $('#list1');

	$list1.jqGrid({
		url: 'knt/list',
		datatype: 'json',
		colNames: [' ', 'Id', 'Имя', 'ИНН', 'КПП', 'Потребитель', 'Поставщик', 'К.договор', 'ERPCode', 'Комментарий', 'Действия'],
		colModel: [
			{name: 'active', index: 'active', width: 20, search: false, hidden: true},
			{name: 'id', index: 'id', width: 55, search: false, hidden: true},
			{name: 'name', index: 'name', width: 200, editable: true},
			{name: 'inn', index: 'inn', width: 100, editable: true},
			{name: 'kpp', index: 'kpp', width: 100, editable: true},
			{name: 'bayer', index: 'bayer', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: '1:0'}, search: false},
			{name: 'supplier', index: 'supplier', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: '1:0'}, search: false},
			{name: 'dog', index: 'dog', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: '1:0'}, search: false},
			{name: 'erpcode', index: 'erpcode', width: 100, editable: true, search: false, hidden: true},
			{name: 'comment', index: 'comment', width: 200, editable: true},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}, search: false, hidden: true}
		],
		autowidth: true,
		rowNum: 10,
		rowList: [10, 20, 50],
		pager: '#pager1',
		sortname: 'id',
		viewrecords: true,
		sortorder: 'asc',
		editurl: 'knt/change',
		caption: 'Справочник контрагентов',
		onSelectRow: function (ids) {
			$('#info_contract').load('route/deprecated/server/knt/info_contract.php?kntid=' + ids);
		}
	});

	$list1.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});
</script>
