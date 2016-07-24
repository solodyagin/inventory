/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

jQuery('#list2').jqGrid({
	url: route + 'controller/server/tmc/libre_vendor.php',
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
	pager: '#pager2',
	sortname: 'id',
	scroll: 1,
	viewrecords: true,
	sortorder: 'asc',
	editurl: route + 'controller/server/tmc/libre_vendor.php',
	caption: 'Справочник производителей'
});
var addOptions = {
	top: 0, left: 0, width: 500
};
jQuery('#list2').jqGrid('setGridHeight', $(window).innerHeight() / 2);
jQuery('#list2').jqGrid('navGrid', '#pager2', {edit: false, add: true, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});