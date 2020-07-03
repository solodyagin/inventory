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
$userid = $req->get('userid');
$addnone = $req->get('addnone');
$orgid = $req->get('orgid');
echo '<select name="suserid" id="suserid">';
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
	$arr = db::prepare($sql)->execute([':orgid' => $orgid])->fetchAll();
	foreach ($arr as $row) {
		$rid = $row['id'];
		$rfio = $row['fio'];
		$sl = ($rid == $userid) ? 'selected' : '';
		echo "<option value=\"$rid\" $sl>$rfio</option>";
	}
} catch (PDOException $ex) {
	throw new dbexception('Не могу выбрать список пользователей', 0, $ex);
}
echo '</select>';
?>
<script>
	$(function(){
		$('.select2').select2({theme: 'bootstrap'});
	});
</script>
