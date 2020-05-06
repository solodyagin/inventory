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
$vendorid = $req->get('vendorid', '1');
$addnone = $req->get('addnone');

echo '<select class="chosen-select" name="svendid" id="svendid">';
if ($addnone == 'true') {
	echo '<option value="-1">не выбрано</option>';
}
try {
	$sql = <<<TXT
select
	vendorid,
	vendor.name
from nome
	inner join vendor on vendor.id = vendorid
where groupid = :groupid
group by vendorid, vendor.name
TXT;
	$rows = db::prepare($sql)->execute([':groupid' => $groupid])->fetchAll();
	foreach ($rows as $row) {
		$rvendorid = $row['vendorid'];
		$rname = $row['name'];
		$sl = ($rvendorid == $vendorid) ? 'selected' : '';
		echo "<option value=\"$rvendorid\" $sl>$rname</option>";
	}
} catch (PDOException $ex) {
	throw new dbexception('Не могу выбрать список групп', 0, $ex);
}
echo '</select>';
