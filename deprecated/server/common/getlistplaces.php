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

// Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

//use PDOException;
use core\db;
use core\dbexception;
use core\request;

$req = request::getInstance();
$orgid = $req->get('orgid', '1');
$placesid = $req->get('placesid', '1');
$addnone = $req->get('addnone');

echo '<select class="chosen-select" name="splaces" id="splaces">';
if ($addnone == 'true') {
	echo '<option value="-1">не выбрано</option>';
}
try {
	$sql = 'select * from places where orgid = :orgid and active = 1 order by name';
	$rows = db::prepare($sql)->execute([':orgid' => $orgid])->fetchAll();
	foreach ($rows as $row) {
		$rid = $row['id'];
		$rname = $row['name'];
		$sl = ($rid == $placesid) ? 'selected' : '';
		echo "<option value=\"$rid\" $sl>$rname</option>";
	}
} catch (PDOException $ex) {
	throw new dbexception('Не могу выбрать список помещений', 0, $ex);
}
echo '</select>';
