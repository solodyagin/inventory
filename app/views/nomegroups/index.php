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
	var addOptions = {top: 0, left: 0, width: 500},
			$list1 = $('#list1'),
			$list2 = $('#list2');

	$list1.jqGrid({
		url: 'nomegroups/list',
		datatype: 'json',
		colNames: [' ', 'Id', 'Имя', 'Комментарий', 'Действия'],
		colModel: [
			{name: 'active', index: 'active', width: 22, fixed: true, sortable: false, search: false},
			{name: 'id', index: 'id', width: 55, hidden: true},
			{name: 'name', index: 'name', width: 200, editable: true,
				unformat: function (cellvalue, options, cell) {
					return $(cell).attr('title');
				}},
			{name: 'comment', index: 'comment', width: 200, editable: true},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
		],
		autowidth: true,
		pager: '#pager1',
		sortname: 'id',
		scroll: 1,
		viewrecords: true,
		sortorder: 'asc',
		editurl: 'nomegroups/change',
		caption: 'Группы номенклатуры',
		onSelectRow: function (id) {
			var caption = 'Параметры группы номенклатуры "' + $list1.jqGrid('getCell', id, 'name') + '"';
			$list2.jqGrid('setCaption', caption);
			$list2.jqGrid('setGridParam', {
				url: 'nomegroupparams/list?groupid=' + id,
				editurl: 'nomegroupparams/change?groupid=' + id
			}).trigger('reloadGrid');
		},
		loadComplete: function () {
			$list2.jqGrid('setCaption', 'Параметры группы номенклатуры');
		}
	});
	$list1.jqGrid('setGridHeight', $(window).innerHeight() / 2);
	$list1.jqGrid('navGrid', '#pager1', {add: true, edit: false, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});

	$list2.jqGrid({
		datatype: 'json',
		colNames: [' ', 'Id', 'Параметр', 'Действия'],
		colModel: [
			{name: 'active', index: 'active', width: 22, fixed: true, sortable: false, search: false},
			{name: 'id', index: 'id', width: 55, hidden: true},
			{name: 'name', index: 'name', width: 200, editable: true},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
		],
		autowidth: true,
		rowNum: 10,
		pager: '#pager2',
		sortname: 'name',
		viewrecords: true,
		sortorder: 'asc',
		caption: 'Параметры группы номенклатуры'
	});
	$list2.jqGrid('setGridHeight', $(window).innerHeight() / 2);
	$list2.jqGrid('navGrid', '#pager2', {add: true, edit: false, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});
</script>
