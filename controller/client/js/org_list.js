/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

jQuery('#o_list').jqGrid({
	url: route + 'controller/server/common/libre_org.php?org_status=list',
	datatype: 'json',
	colNames: [' ', 'Id', 'Имя организации', 'Действия'],
	colModel: [
		{name: 'active', index: 'active', width: 20},
		{name: 'id', index: 'id', width: 55},
		{name: 'name', index: 'name', width: 400, editable: true},
		{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
	],
	onSelectRow: function (ids) {
		$('#pg_add_edit').load(route + 'controller/server/common/getphotoorg.php?eqid=' + ids);
		$('#simple-btn').css('visibility', 'visible');
		$('#simple-btn').fileapi('data', {geteqid: ids});
	},
	autowidth: true,
	pager: '#o_pager',
	sortname: 'id',
	scroll: 1,
	viewrecords: true,
	sortorder: 'asc',
	editurl: route + 'controller/server/common/libre_org.php?org_status=edit',
	caption: 'Справочник организаций'
});
jQuery('#o_list').jqGrid('setGridHeight', $(window).innerHeight() / 2);
jQuery('#o_list').jqGrid('navGrid', '#o_pager', {edit: false, add: true, del: false, search: false}, {}, {}, {}, {multipleSearch: false}, {closeOnEscape: true});

$('#simple-btn').fileapi({
	url: route + 'controller/server/common/uploadimageorg.php',
	data: {'geteqid': 0},
	multiple: true,
	maxSize: 20 * FileAPI.MB,
	autoUpload: true,
	onFileComplete: function (evt, uiEvt) {
		if (uiEvt.result.msg != '') {
			$().toastmessage('showErrorToast', 'Ошибка загрузки файла:' + uiEvt.result.msg);
		}
	},
	elements: {
		size: '.js-size',
		active: {show: '.js-upload', hide: '.js-browse'},
		progress: '.js-progress'
	}
});
