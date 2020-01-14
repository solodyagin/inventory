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
		pager: '#pager1',
		sortname: 'id',
		scroll: 1,
		viewrecords: true,
		sortorder: 'asc',
		editurl: 'orglist/change'
	});
	$list1.jqGrid('setGridHeight', $(window).innerHeight() / 2);
	$list1.jqGrid('navGrid', '#pager1', {edit: false, add: true, del: false, search: false}, {}, {}, {}, {multipleSearch: false}, {closeOnEscape: true});
</script>
