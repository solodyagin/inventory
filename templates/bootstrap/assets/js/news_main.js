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

var pznews = 0;
var url = 'news/getnews?num=0';

$('#newsprev').click(function () {
	if (pznews >= 1) {
		pznews--;
		url = 'news/getnews?num=' + pznews;
		$('#newslist').load(url);
	}
});

$('#newsnext').click(function () {
	pznews++;
	url = 'news/getnews?num=' + pznews;
	$prev = $('#newsnext').html();
	$('#newslist').load(url);
});

$('#newslist').load(url);
