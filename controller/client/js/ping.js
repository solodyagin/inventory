/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

$('#test_ping').click(function () {
	$('#ping_add').html('<img src="controller/client/themes/' + theme + '/img/loading.gif">');
	$('#ping_add').load(route + 'controller/server/common/ping.php?orgid=' + defaultorgid);
});
