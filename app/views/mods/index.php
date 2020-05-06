<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="row">
		<div class="col-xs-12 col-md-12 col-sm-12">
			<table id="list1"></table>
			<div id="pager1"></div>
		</div>
	</div>
</div>
<script>
	var $list1 = $('#list1');
	$list1.jqGrid({
		url: 'mods/list',
		editurl: 'mods/change',
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
		pager: '#pager1',
		sortname: 'name',
		sortorder: 'asc',
		rowNum: 30,
		viewrecords: true
	});
	$list1.jqGrid('navGrid', '#pager1', {edit: false, add: false, del: false, search: false});
	$list1.jqGrid('setGridHeight', $(window).innerHeight() / 2);
</script>
