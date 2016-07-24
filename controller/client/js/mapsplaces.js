/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

// Получаем список ТМЦ в выбранной организации, в выбранном помещении
function GetListTmc(placesid) {
	url = route + 'controller/server/map/getlisttmc.php?placesid=' + placesid + '&addnone=false';
	$.get(url, function (data) {
		$('#sel_tmc').html(data);
		UpdateChosen();
	});
}

// при выборе помещения
$('#splaces').click(function () {
	GetListTmc($('#splaces :selected').val());
});

GetListTmc($('#splaces :selected').val());

