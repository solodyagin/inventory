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
	$('#list1').jqGrid({
		url: 'vendors/list',
		datatype: 'json',
		colNames: [' ', 'Id', 'Имя', 'Комментарий', 'Действия'],
		colModel: [
			{name: 'active', index: 'active', width: 22, fixed: true, sortable: false, search: false},
			{name: 'id', index: 'id', width: 55, hidden: true},
			{name: 'name', index: 'name', width: 200, editable: true},
			{name: 'comment', index: 'comment', width: 200, editable: true},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
		],
		autowidth: true,
		pager: '#pager1',
		sortname: 'name',
		scroll: 1,
		viewrecords: true,
		sortorder: 'asc',
		editurl: 'vendors/change',
		caption: 'Справочник производителей'
	});
	var addOptions = {
		top: 0, left: 0, width: 500
	};
	$('#list1').jqGrid('setGridHeight', $(window).innerHeight() / 2);
	$('#list1').jqGrid('navGrid', '#pager1', {edit: false, add: true, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});
</script>
