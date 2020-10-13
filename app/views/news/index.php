<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="row">
		<div class="col-xs-12 col-md-12 col-sm-12">
			<table id="list1"></table>
			<div id="pager1"></div>
			<div id="pg_add_edit"></div>
		</div>
	</div>
</div>
<script>
	var $list1 = $('#list1');
	var $bmd = $('#bmd_iframe');

	$list1.jqGrid({
		url: 'news/list',
		editurl: 'news/change',
		datatype: 'json',
		colNames: ['Id', 'Дата', 'Заголовок', 'Закреплено', 'Действия'],
		colModel: [
			{name: 'id', index: 'id', width: 55, editable: false, hidden: true},
			{name: 'dt', index: 'dt', width: 60, editable: false},
			{name: 'title', index: 'title', width: 200, editable: true},
			{name: 'stiker', index: 'stiker', width: 200, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: '1:0'}},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
		],
		autowidth: true,
		pager: '#pager1',
		sortname: 'dt',
		sortorder: 'desc',
		rowNum: 30,
		viewrecords: true
	});

	$list1.jqGrid('navGrid', '#pager1', {edit: false, add: false, del: false, search: false});

	$list1.jqGrid('navButtonAdd', '#pager1', {
		caption: '<i class="fas fa-plus-circle"></i>',
		title: 'Добавить',
		buttonicon: 'none',
		onClickButton: function () {
			$bmd.bmdIframe({
				title: 'Добавление новости',
				src: 'news/news?step=add'
			}).modal();
		}
	});

	$list1.jqGrid('navButtonAdd', '#pager1', {
		caption: '<i class="fas fa-edit"></i>',
		title: 'Отредактировать',
		buttonicon: 'none',
		onClickButton: function () {
			var gsr = $list1.jqGrid('getGridParam', 'selrow');
			if (gsr) {
				$bmd.bmdIframe({
					title: 'Редактирование новости',
					src: 'news/news?step=edit&id=' + gsr
				}).modal();
			} else {
				$.notify('Сначала выберите строку!');
			}
		}
	});

	$list1.jqGrid('setGridHeight', $(window).innerHeight() / 2);
</script>
