<?php

/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$orgid = GetDef('orgid', '1');
$userid = GetDef('userid', '1');
$addnone = GetDef('addnone');
$dopname = GetDef('dopname');
$chosen = GetDef('chosen', 'false');

echo '<select class="chosen-select" name=suserid' . $dopname . " id=suserid" . $dopname . ">";
if ($addnone == 'true') {
	echo '<option value="-1">не выбрано</option>';
}
$sql = <<<TXT
SELECT users.id,users.login,users_profile.fio
FROM   users
       INNER JOIN users_profile
               ON users.id = users_profile.usersid
WHERE  users.orgid = :orgid
       AND users.active = 1
ORDER  BY users.login
TXT;
try {
	$arr = DB::prepare($sql)->execute(array(':orgid' => $orgid))->fetchAll();
	foreach ($arr as $row) {
		$sl = ($row['id'] == $userid) ? 'selected' : '';
		echo "<option value=\"{$row['id']}\" $sl>{$row['fio']} ({$row['login']})</option>";
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список пользователей', 0, $ex);
}
echo '</select>';

if ($chosen == 'true') {
	echo '<script>for (var selector in config) {$(selector).chosen(config[selector]);}</script>';
}
