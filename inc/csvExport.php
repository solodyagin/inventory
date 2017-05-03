<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename=file.xls');
header('Pragma: no-cache');

$buffer = $_POST['csvBuffer'];

try {
	echo $buffer;
} catch (Exception $e) {

}
