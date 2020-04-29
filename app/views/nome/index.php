<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="row">
		<div class="col-xs-12 col-md-12 col-sm-12">
			<table id="list1"></table>
			<div id="pager1"></div>
			<div id="add_edit"></div>
		</div>
	</div>
</div>
<script>
	var $list1 = $('#list1');
	$list1.jqGrid({
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
	$list1.jqGrid('setGridHeight', $(window).innerHeight() / 2);
	$list1.jqGrid('navGrid', '#pager1', {edit: false, add: false, del: false, search: false});
	$list1.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});
	$list1.jqGrid('navButtonAdd', '#pager1', {
		buttonicon: 'none',
		caption: '<i class="fas fa-plus-circle"></i>',
		onClickButton: function () {
			$('#add_edit').empty().dialog({
				title: 'Добавление номенклатуры',
				autoOpen: false,
				modal: true,
				height: 280,
				width: 640,
				open: function () {
					$(this).load('route/deprecated/client/view/tmc/nome_add_edit.php?step=add');
				}
			}).dialog('open');
		}
	});
	$list1.jqGrid('navButtonAdd', '#pager1', {
		buttonicon: 'none',
		caption: '<i class="fas fa-edit"></i>',
		onClickButton: function () {
			var gsr = $list1.jqGrid('getGridParam', 'selrow');
			if (gsr) {
				$('#add_edit').empty().dialog({
					title: 'Редактирование номенклатуры',
					autoOpen: false,
					modal: true,
					height: 280,
					width: 640,
					open: function () {
						$(this).load('route/deprecated/client/view/tmc/nome_add_edit.php?step=edit&id=' + gsr);
					}
				}).dialog('open');
			} else {
				$().toastmessage('showWarningToast', 'Сначала выберите строку!');
			}
		}
	});
</script>
