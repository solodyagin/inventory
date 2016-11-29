/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

var addOptions = {
	top: 0, left: 0, width: 500
};
jQuery('#list2').jqGrid({
	url: route + 'controller/server/tmc/libre_group.php?fix=1',
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
	pager: '#pager2',
	sortname: 'id',
	scroll: 1,
	height: 140,
	viewrecords: true,
	sortorder: 'asc',
	editurl: route + 'controller/server/tmc/libre_group.php?fix=1',
	caption: 'Группы номенклатуры',
	onSelectRow: function (ids) {
		if (ids == null) {
			ids = 0;
			if (jQuery('#list10_d').jqGrid('getGridParam', 'records') > 0) {
				jQuery('#list10_d').jqGrid('setGridParam', {url: route + 'controller/server/tmc/libre_group_sub.php?q=1&groupid=' + ids, page: 1});
				jQuery('#list10_d').jqGrid('setGridParam', {editurl: route + 'controller/server/tmc/libre_group_sub.php?q=1&groupid=' + ids, page: 1})
						.trigger('reloadGrid');
			}
		} else {
			jQuery('#list10_d').jqGrid('setGridParam', {url: route + 'controller/server/tmc/libre_group_sub.php?q=1&groupid=' + ids, page: 1});
			jQuery('#list10_d').jqGrid('setGridParam', {editurl: route + 'controller/server/tmc/libre_group_sub.php?q=1&groupid=' + ids, page: 1})
					.trigger('reloadGrid');
		}
	}
});
jQuery('#list2').jqGrid('setGridHeight', $(window).innerHeight() / 2);
jQuery('#list2').jqGrid('navGrid', '#pager2', {edit: false, add: true, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});

jQuery('#list10_d').jqGrid({
	height: 100,
	autowidth: true,
	url: route + 'controller/server/tmc/libre_group_sub.php?fix=1',
	datatype: 'json',
	colNames: [' ', 'Id', 'Параметр', 'Действия'],
	colModel: [
		{name: 'active', index: 'active', width: 20},
		{name: 'id', index: 'id', width: 55, hidden: true},
		{name: 'name', index: 'name', width: 200, editable: true},
		{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
	],
	rowNum: 5,
	pager: '#pager10_d',
	sortname: 'id',
	scroll: 1,
	viewrecords: true,
	sortorder: 'asc',
	caption: 'Параметры группы номенклатуры'
}).navGrid('#pager10_d', {add: true, edit: false, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});
