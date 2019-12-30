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

$('#orgs').change(function () {
	var exdate = new Date();
	exdate.setDate(exdate.getDate() + 365);
	orgid = $('#orgs :selected').val();
	document.cookie = 'defaultorgid=' + orgid + '; path=/; expires=' + exdate.toUTCString();
});

$('#fontsize').change(function () {
	var exdate = new Date();
	exdate.setDate(exdate.getDate() + 365);
	fontsize = $('#fontsize :selected').val();
	document.cookie = 'fontsize=' + fontsize + '; path=/; expires=' + exdate.toUTCString();
});
