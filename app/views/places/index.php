<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="row">
		<div class="col-xs-12 col-md-6 col-sm-6">
			<table id="list1"></table>
			<div id="pager1"></div>
		</div>
		<div class="col-xs-12 col-md-6 col-sm-6">
			<table id="list2"></table>
			<div id="pager2"></div>
		</div>
	</div>
</div>
<script>
	var $list1 = $('#list1'), $list2 = $('#list2');

	$list1.jqGrid({
		url: 'places/list?orgid=' + defaultorgid,
		datatype: 'json',
		colNames: [' ', 'Id', 'Подразделение', 'Наименование', 'Комментарий', 'Действия'],
		colModel: [
			{name: 'active', index: 'active', width: 10},
			{name: 'id', index: 'id', width: 55, hidden: true},
			{name: 'opgroup', index: 'opgroup', width: 100, editable: true},
			{name: 'name', index: 'name', width: 200, editable: true},
			{name: 'comment', index: 'comment', width: 200, editable: true},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
		],
		grouping: true,
		groupingView: {
			groupField: ['opgroup'],
			groupColumnShow: [true],
			groupText: ['<b>{0}</b>'],
			groupOrder: ['asc'],
			groupSummary: [false],
			groupCollapse: false
		},
		autowidth: true,
		pager: '#pager1',
		sortname: 'id',
		scroll: 1,
		viewrecords: true,
		sortorder: 'asc',
		editurl: 'places/change?orgid=' + defaultorgid,
		caption: 'Помещения',
		onSelectRow: function (id) {
			var caption = 'Рабочие места в помещении "' + $list1.jqGrid('getCell', id, 'name') + '"';
			$list2.jqGrid('setCaption', caption);
			$list2.jqGrid('setGridParam', {url: 'places/listsub?placesid=' + id + '&orgid=' + defaultorgid});
			$list2.jqGrid('setGridParam', {editurl: 'places/changesub?placesid=' + id + '&orgid=' + defaultorgid});
			$list2.trigger('reloadGrid');
		},
		loadComplete: function () {
			$list2.jqGrid('setCaption', 'Рабочие места');
			$list2.jqGrid('setGridParam', {
				url: 'places/listsub',
				editurl: 'places/listsub'
			}).trigger('reloadGrid');
		}
	}).navGrid('#pager1', {add: true, edit: false, del: false, search: false}, {}, {}, {}, {multipleSearch: false}, {closeOnEscape: true});
	$list1.jqGrid('setGridHeight', $(window).innerHeight() / 2);

	$list2.jqGrid({
		height: 100,
		autowidth: true,
		url: 'places/listsub',
		datatype: 'json',
		colNames: ['Id', 'Сотрудник', 'Действия'],
		colModel: [
			{name: 'places_users.id', index: 'places_users.id', width: 10, hidden: true},
			{name: 'name', index: 'name', width: 200, editable: true, edittype: 'select', editoptions: {
					editrules: {required: true},
					dataUrl: 'route/deprecated/server/common/getlistusers.php?orgid=' + defaultorgid
				}},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
		],
		rowNum: 10,
		rowList: [10, 20, 50],
		pager: '#pager2',
		sortname: 'places_users.id',
		viewrecords: true,
		sortorder: 'asc',
		caption: 'Рабочие места'
	}).navGrid('#pager2', {add: true, edit: false, del: false, search: false}, {}, {}, {}, {multipleSearch: false}, {closeOnEscape: true});
	$list2.jqGrid('setGridHeight', $(window).innerHeight() / 2);
</script>
