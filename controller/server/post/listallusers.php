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

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

$userid = GetDef('userid');
$addnone = GetDef('addnone');
$orgid = GetDef('orgid');

echo '<select name="suserid" id="suserid">';
if ($addnone == 'true') {
	echo '<option value="-1">не выбрано</option>';
}
$sql = 'SELECT * FROM users WHERE active = 1 AND orgid = :orgid ORDER BY login';
try {
	$arr = DB::prepare($sql)->execute(array(':orgid' => $orgid))->fetchAll();
	foreach ($arr as $row) {
		$z = $row['id'];
		$zx = new BaseUser();
		$zx->getById($z);
		$sl = ($z == $userid) ? 'selected' : '';
		echo "<option value=\"$z\" $sl>$zx->fio({$row['login']})</option>";
		unset($zx);
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список пользователей', 0, $ex);
}

echo '</select>';
?>
<script>
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
</script>
