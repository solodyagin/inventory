/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

var pznews = 0;

$('#newsprev').click(function () {
	if (pznews >= 1) {
		pznews--;
		url = route + 'controller/server/news/getnews.php?num=' + pznews;
		$('#newslist').load(url);
	}
});

$('#newsnext').click(function () {
	pznews++;
	url = route + 'controller/server/news/getnews.php?num=' + pznews;
	$prev = $('#newsnext').html();
	$('#newslist').load(url, function (responseText, textStatus, XMLHttpRequest) {
		if (responseText == 'error') {
			pznews--;
			url = route + 'controller/server/news/getnews.php?num=' + pznews;
			$('#newslist').load(url);
		}
	});
});

url = route + 'controller/server/news/getnews.php?num=0';
$('#newslist').load(url);
