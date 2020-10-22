<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="row">
		<div class="col-xs-12 col-md-12 col-sm-12">
			<table id="grid1"></table>
			<div id="pager1"></div>
		</div>
	</div>
</div>
<script>
	var $grid1 = $('#grid1'),
			$bmd = $('#bmd_iframe');

	$grid1.jqGrid({
		url: 'nome/list',
		datatype: 'json',
		colNames: [' ', 'Id', 'Группа', 'Производитель', 'Наименование', ''],
		colModel: [
			{name: 'active', index: 'active', width: 22, fixed: true, sortable: false, search: false},
			{name: 'nomeid', index: 'nomeid', width: 55, hidden: true},
			{name: 'group_nome.name', index: 'group_nome.name', width: 200},
			{name: 'vendor.name', index: 'vendor.name', width: 200},
			{name: 'nomename', index: 'nome.name', width: 200, editable: true},
			{name: 'myac', width: 70, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}, search: false}
		],
		autowidth: true,
		height: 200,
		grouping: true,
		groupingView: {
			groupText: ['<b>{0} - {1} Item(s)</b>'],
			groupCollapse: true,
			groupField: ['group_nome.name']
		},
		pager: '#pager1',
		sortname: 'nomeid',
		viewrecords: true,
		rowNum: 1000,
		scroll: 1,
		sortorder: 'asc',
		editurl: 'nome/change'
	});
	$grid1.jqGrid('setGridHeight', $(window).innerHeight() / 2);
	$grid1.jqGrid('navGrid', '#pager1', {edit: false, add: false, del: false, search: false});
	$grid1.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});
	$grid1.jqGrid('navButtonAdd', '#pager1', {
		buttonicon: 'none',
		caption: '<i class="fas fa-plus-circle"></i>',
		onClickButton: function () {
			$bmd.bmdIframe({
				title: 'Добавление номенклатуры',
				src: 'nome/add'
			}).modal();
		}
	});
	$grid1.jqGrid('navButtonAdd', '#pager1', {
		buttonicon: 'none',
		caption: '<i class="fas fa-edit"></i>',
		onClickButton: function () {
			var gsr = $grid1.jqGrid('getGridParam', 'selrow');
			if (gsr) {
				$bmd.bmdIframe({
					title: 'Редактирование номенклатуры',
					src: 'nome/edit?id=' + gsr
				}).modal();
			} else {
				$.notify('Сначала выберите строку!');
			}
		}
	});
</script>
