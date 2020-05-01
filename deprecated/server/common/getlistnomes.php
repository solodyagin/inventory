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
$groupid = $req->get('groupid', '1');
$vendorid = $req->get('vendorid', 1);
if ($vendorid == '') {
	$vendorid = 1;
}
$nomeid = $req->get('nomeid');

echo '<select class="chosen-select" name="snomeid" id="snomeid">';
try {
	$sql = 'select id, name from nome where groupid = :groupid and vendorid = :vendorid';
	$rows = db::prepare($sql)->execute([':groupid' => $groupid, ':vendorid' => $vendorid])->fetchAll();
	foreach ($rows as $row) {
		$rid = $row['id'];
		$rname = $row['name'];
		$sl = ($rid == $nomeid) ? 'selected' : '';
		echo "<option value=\"$rid\" $sl>$rname</option>";
	}
} catch (PDOException $ex) {
	throw new dbexception('Не могу выбрать список номенклатуры', 0, $ex);
}
echo '</select>';
