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
<div id="add_edit"></div>
<script>
	var $list1 = $('#list1'), $list2 = $('#list2');

	$list1.jqGrid({
		url: 'peoples/list',
		datatype: 'json',
		colNames: [' ', 'Id', 'Организация', 'ФИО', 'Логин', 'E-mail', 'Администратор', 'Действия'],
		colModel: [
			{name: 'active', index: 'active', width: 22, fixed: true, sortable: false, search: false},
			{name: 'usersid', index: 'u.id', width: 55, hidden: true},
			{name: 'orgname', index: 'o.name', width: 60},
			{name: 'fio', index: 'fio', width: 45},
			{name: 'login', index: 'login', width: 45, editable: true},
			{name: 'email', index: 'email', width: 30, editable: true},
			{name: 'mode', index: 'mode', width: 30, editable: true, edittype: 'checkbox', editoptions: {value: 'Да:Нет'}, search: false},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}, search: false}
		],
		onSelectRow: function (id) {
			var caption = 'Набор прав сотрудника "' + $list1.jqGrid('getCell', id, 'fio') + '"';
			$list2.jqGrid('setCaption', caption);
			$list2.jqGrid('setGridParam', {
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
			$list2.jqGrid('setCaption', 'Набор прав доступа сотрудника');
			$list2.jqGrid('setGridParam', {
				url: 'roles/list?userid=0',
				editurl: 'roles/change?userid=0'
			}).trigger('reloadGrid');
		}
	});
	$list1.jqGrid('setGridHeight', $(window).innerHeight() / 2);
	$list1.jqGrid('navGrid', '#pager1', {edit: false, add: false, del: false, search: false});
	$list1.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});

	$list1.jqGrid('navButtonAdd', '#pager1', {
		caption: '<i class="fas fa-tag"></i>',
		title: 'Выбор колонок',
		buttonicon: 'none',
		onClickButton: function () {
			$list1.jqGrid('columnChooser', {
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

	$list1.jqGrid('navButtonAdd', '#pager1', {
		caption: '<i class="fas fa-user-plus"></i>',
		title: 'Добавить',
		buttonicon: 'none',
		onClickButton: function () {
			$('#add_edit').dialog({
				autoOpen: false,
				height: 600,
				width: 780,
				modal: true,
				title: 'Добавление сотрудника'
			}).dialog('open');
			$('#add_edit').load('peoples/add');
		}
	});

	$list1.jqGrid('navButtonAdd', '#pager1', {
		caption: '<i class="fas fa-user-md"></i>',
		title: 'Изменить данные',
		buttonicon: 'none',
		onClickButton: function () {
			var gsr = $list1.jqGrid('getGridParam', 'selrow');
			if (gsr) {
				$('#add_edit').dialog({
					autoOpen: false,
					height: 600,
					width: 780,
					modal: true,
					title: 'Редактирование сотрудника'
				}).dialog('open');
				$('#add_edit').load('peoples/edit?id=' + gsr);
			} else {
				$.notify('Сначала выберите строку!');
			}
		}
	});

	$list1.jqGrid('navButtonAdd', '#pager1', {
		caption: '<i class="fas fa-id-card"></i>',
		title: 'Профиль',
		buttonicon: 'none',
		onClickButton: function () {
			var gsr = $list1.jqGrid('getGridParam', 'selrow');
			if (gsr) {
				$('#add_edit').dialog({
					autoOpen: false,
					height: 600,
					width: 780,
					modal: true,
					title: 'Редактирование профиля'
				}).dialog('open');
				$('#add_edit').load('route/deprecated/client/view/users/profile_add_edit.php?userid=' + gsr);
			} else {
				$.notify('Сначала выберите строку!');
			}
		}
	});

	var addOptions = {
		top: 0, left: 0, width: 500
	};

	$list2.jqGrid({
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
