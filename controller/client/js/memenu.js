/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

$('#orgs').change(function () {
	var exdate = new Date();
	exdate.setDate(exdate.getDate() + 365);
	orgid = $('#orgs :selected').val();
	document.cookie = 'defaultorgid=' + orgid + '; path=/; expires=' + exdate.toUTCString();
});

$('#stl').change(function () {
	var exdate = new Date();
	exdate.setDate(exdate.getDate() + 365);
	stl = $('#stl :selected').val();
	document.cookie = 'stl=' + stl + '; path=/; expires=' + exdate.toUTCString();
});
