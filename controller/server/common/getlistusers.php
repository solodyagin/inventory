<?php

/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$orgid = GetDef('orgid', '1');
$userid = GetDef('userid', '1');
$addnone = GetDef('addnone');
$dopname = GetDef('dopname');
$chosen = GetDef('chosen', 'false');

$sql = <<<TXT
SELECT users.id,users.login,users_profile.fio
FROM   users
       INNER JOIN users_profile
               ON users.id = users_profile.usersid
WHERE  users.orgid = '$orgid'
       AND users.active = 1
ORDER  BY users.login
TXT;
$result = $sqlcn->ExecuteSQL($sql) or
		die('Не могу выбрать список пользователей!' . mysqli_error($sqlcn->idsqlconnection));
echo '<select class="chosen-select" name=suserid' . $dopname . " id=suserid" . $dopname . ">";
if ($addnone == 'true') {
	echo '<option value="-1">нет выбора</option>';
}
while ($row = mysqli_fetch_array($result)) {
	$sl = ($row['id'] == $userid) ? 'selected' : '';
	echo "<option value=\"{$row['id']}\" $sl>{$row['fio']} ({$row['login']})</option>";
}
echo '</select>';
if ($chosen == 'true') {
	echo '<script>for (var selector in config) {$(selector).chosen(config[selector]);}</script>';
}
