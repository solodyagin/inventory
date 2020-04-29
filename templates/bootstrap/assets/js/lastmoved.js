/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчик: Грибов Павел
 * Сайт: http://грибовы.рф
 */
/*
 * Inventory - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчик: Сергей Солодягин (solodyagin@gmail.com)
 */

$('#tbl_move').jqGrid({
	url: 'moveinfo/list?eqid=',
	datatype: 'json',
	colNames: ['Id', 'Дата', 'Организация', 'Помещение', 'Сотрудник', 'Организация', 'Помещение', 'Сотрудник', 'Оргтехника', 'Комментарий'],
	colModel: [
		{name: 'id', index: 'id', width: 25, hidden: true},
		{name: 'dt', index: 'dt', width: 100},
		{name: 'orgname1', index: 'orgname1', width: 140, hidden: true},
		{name: 'place1', index: 'place1', width: 90},
		{name: 'user1', index: 'user1', width: 90},
		{name: 'orgname2', index: 'orgname2', width: 140, hidden: true},
		{name: 'place2', index: 'place2', width: 90},
		{name: 'user2', index: 'user2', width: 90},
		{name: 'name', index: 'name', width: 120},
		{name: 'comment', index: 'comment', width: 100, editable: true}
	],
	autowidth: true,
	pager: '#pager2',
	sortname: 'dt',
	scroll: true,
	shrinkToFit: true,
	height: 200,
	sortorder: 'desc'
});

$('#tbl_move').jqGrid('destroyGroupHeader');
$('#tbl_move').jqGrid('setGroupHeaders', {
	useColSpanStyle: true,
	groupHeaders: [
		{startColumnName: 'orgname1', numberOfColumns: 3, titleText: 'Откуда'},
		{startColumnName: 'orgname2', numberOfColumns: 3, titleText: 'Куда'}
	]
});
