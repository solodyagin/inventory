<?php

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

header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename=file.xls');
header('Pragma: no-cache');

$buffer = $_POST['csvBuffer'];

try {
	echo $buffer;
} catch (Exception $e) {

}
