<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="row">
		<div class="col-xs-12 col-md-7 col-sm-7">
			<table id="list1"></table>
			<div id="pager1"></div>
		</div>
		<div class="col-xs-12 col-md-5 col-sm-5">
			<table id="list2"></table>
			<div id="pager2"></div>
		</div>
	</div>
</div>
<script>
	var addOptions = {
		top: 0, left: 0, width: 500
	};
	$('#list1').jqGrid({
		url: 'route/deprecated/server/tmc/libre_group.php',
		datatype: 'json',
		colNames: [' ', 'Id', 'Имя', 'Комментарий', 'Действия'],
		colModel: [
			{name: 'active', index: 'active', width: 20},
			{name: 'id', index: 'id', width: 55, hidden: true},
			{name: 'name', index: 'name', width: 200, editable: true},
			{name: 'comment', index: 'comment', width: 200, editable: true},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
		],
		autowidth: true,
		rowNum: 50,
		pager: '#pager1',
		sortname: 'id',
		scroll: 1,
		/*height: 140,*/
		viewrecords: true,
		sortorder: 'asc',
		editurl: 'route/deprecated/server/tmc/libre_group.php',
		caption: 'Группы номенклатуры',
		onSelectRow: function (id) {
			$('#list2').jqGrid('setGridParam', {
				url: 'route/deprecated/server/tmc/libre_group_sub.php?q=1&groupid=' + id,
				editurl: 'route/deprecated/server/tmc/libre_group_sub.php?q=1&groupid=' + id,
				page: 1
			}).trigger('reloadGrid');
		}
	});
	$('#list1').jqGrid('setGridHeight', $(window).innerHeight() / 2);
	$('#list1').jqGrid('navGrid', '#pager1', {edit: false, add: true, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});

	$('#list2').jqGrid({
		height: 100,
		autowidth: true,
		url: 'route/deprecated/server/tmc/libre_group_sub.php',
		datatype: 'json',
		colNames: [' ', 'Id', 'Параметр', 'Действия'],
		colModel: [
			{name: 'active', index: 'active', width: 20},
			{name: 'id', index: 'id', width: 55, hidden: true},
			{name: 'name', index: 'name', width: 200, editable: true},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
		],
		rowNum: 5,
		pager: '#pager2',
		sortname: 'id',
		scroll: 1,
		viewrecords: true,
		sortorder: 'asc',
		caption: 'Параметры группы номенклатуры'
	}).navGrid('#pager2', {add: true, edit: false, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});
</script>
