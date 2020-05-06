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
$userid = $req->get('userid', '1');
$addnone = $req->get('addnone');
$dopname = $req->get('dopname');
$chosen = $req->get('chosen', 'false');

echo '<select class="chosen-select" name=suserid' . $dopname . " id=suserid" . $dopname . ">";
if ($addnone == 'true') {
	echo '<option value="-1">не выбрано</option>';
}
try {
	$sql = <<<TXT
select
	users.id,
	users.login,
	users_profile.fio
from users
	inner join users_profile on users.id = users_profile.usersid
where users.orgid = :orgid
	and users.active = 1
order by users.login
TXT;
	$rows = db::prepare($sql)->execute([':orgid' => $orgid])->fetchAll();
	foreach ($rows as $row) {
		$rid = $row['id'];
		$rfio = $row['fio'];
		$rlogin = $row['login'];
		$sl = ($rid == $userid) ? 'selected' : '';
		echo "<option value=\"$rid\" $sl>$rfio ($rlogin)</option>";
	}
} catch (PDOException $ex) {
	throw new dbexception('Не могу выбрать список пользователей', 0, $ex);
}
echo '</select>';
if ($chosen == 'true') {
	echo <<<TXT
<script>
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
</script>
TXT;
}
