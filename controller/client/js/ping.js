/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

$('#test_ping').click(function () {
	$('#ping_add').html('<img src="controller/client/themes/' + theme + '/img/loading.gif">');
	$('#ping_add').load(route + 'controller/server/common/ping.php?orgid=' + defaultorgid);
});
