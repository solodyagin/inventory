<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="row">
		<div class="col-xs-12 col-md-7 col-sm-7">
			<table id="grid1"></table>
			<div id="pager1"></div>
		</div>
		<div class="col-xs-12 col-md-5 col-sm-5">
			<table id="grid2"></table>
			<div id="pager2"></div>
		</div>
	</div>
</div>
<script>
	var $grid1 = $('#grid1'),
			$grid2 = $('#grid2'),
			$bmd = $('#bmd_iframe');

	$grid1.jqGrid({
		url: 'peoples/list',
		datatype: 'json',
		colNames: ['', 'Id', 'Организация', 'ФИО', 'Логин', 'E-mail', 'Администратор', 'Действия'],
		colModel: [
			{name: 'active', index: 'active', width: 22, fixed: true, sortable: false, search: false},
			{name: 'usersid', index: 'u.id', width: 55, hidden: true},
			{name: 'orgname', index: 'o.name', width: 60},
			{name: 'fio', index: 'fio', width: 45},
			{name: 'login', index: 'login', width: 45, editable: true},
			{name: 'email', index: 'email', width: 30, editable: true},
			{name: 'mode', index: 'mode', width: 30, editable: true, edittype: 'checkbox', editoptions: {value: 'Да:Нет'}, search: false},
			{name: 'myac', width: 70, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}, search: false}
		],
		onSelectRow: function (id) {
			var caption = 'Набор прав сотрудника "' + $grid1.jqGrid('getCell', id, 'fio') + '"';
			$grid2.jqGrid('setCaption', caption);
			$grid2.jqGrid('setGridParam', {
				url: 'roles/list?userid=' + id + '&orgid=' + defaultorgid,
				editurl: 'roles/change?userid=' + id + '&orgid=' + defaultorgid
			}).trigger('reloadGrid');
		},
		autowidth: true,
		scroll: 1,
		pager: '#pager1',
		sortname: 'fio',
		sortorder: 'asc',
		viewrecords: true,
		editurl: 'peoples/change',
		caption: 'Справочник сотрудников',
		loadComplete: function () {
			$grid2.jqGrid('setCaption', 'Набор прав доступа сотрудника');
			$grid2.jqGrid('setGridParam', {
				url: 'roles/list?userid=0',
				editurl: 'roles/change?userid=0'
			}).trigger('reloadGrid');
		}
	});
	$grid1.jqGrid('setGridHeight', $(window).innerHeight() / 2);
	$grid1.jqGrid('navGrid', '#pager1', {edit: false, add: false, del: false, search: false});
	$grid1.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});

	$grid1.jqGrid('navButtonAdd', '#pager1', {
		caption: '<i class="fas fa-tag"></i>',
		title: 'Выбор колонок',
		buttonicon: 'none',
		onClickButton: function () {
			$grid1.jqGrid('columnChooser', {
				width: 550,
				dialog_opts: {
					modal: true,
					minWidth: 470,
					height: 470
				},
				msel_opts: {
					dividerLocation: 0.5
				}
			});
		}
	});

	$grid1.jqGrid('navButtonAdd', '#pager1', {
		caption: '<i class="fas fa-user-plus"></i>',
		title: 'Добавить',
		buttonicon: 'none',
		onClickButton: function () {
			$bmd.bmdIframe({
				title: 'Добавление сотрудника',
				src: 'peoples/add'
			}).modal();
		}
	});

	$grid1.jqGrid('navButtonAdd', '#pager1', {
		caption: '<i class="fas fa-user-md"></i>',
		title: 'Изменить данные',
		buttonicon: 'none',
		onClickButton: function () {
			var gsr = $grid1.jqGrid('getGridParam', 'selrow');
			if (gsr) {
				$bmd.bmdIframe({
					title: 'Редактирование сотрудника',
					src: 'peoples/edit?id=' + gsr
				}).modal();
			} else {
				$.notify('Сначала выберите строку!');
			}
		}
	});

	$grid1.jqGrid('navButtonAdd', '#pager1', {
		caption: '<i class="fas fa-id-card"></i>',
		title: 'Профиль',
		buttonicon: 'none',
		onClickButton: function () {
			var gsr = $grid1.jqGrid('getGridParam', 'selrow');
			if (gsr) {
				$bmd.bmdIframe({
					title: 'Редактирование сотрудника',
					src: 'peoples/profile?id=' + gsr
				}).modal();
			} else {
				$.notify('Сначала выберите строку!');
			}
		}
	});

	var addOptions = {
		top: 0, left: 0, width: 500
	};

	$grid2.jqGrid({
		autowidth: true,
		url: 'roles/list?userid=0',
		datatype: 'json',
		colNames: ['Id', 'Права доступа', 'Действия'],
		colModel: [
			{name: 'id', index: 'id', width: 55, hidden: true},
			{name: 'role', index: 'role', width: 200, editable: true, edittype: 'select', editoptions: {
					editrules: {required: true},
					dataUrl: 'peoples/getlistroles?orgid=' + defaultorgid
				}},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
		],
		rowNum: 10,
		rowList: [10, 20, 30],
		pager: '#pager2',
		sortname: 'role',
		sortorder: 'asc',
		viewrecords: true,
		caption: 'Набор прав доступа сотрудника'
	}).navGrid('#pager2', {add: true, edit: false, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});
</script>
