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

$userid = GetDef('userid');
$addnone = GetDef('addnone');
$orgid = GetDef('orgid');

$sql = "SELECT * FROM users WHERE active = 1 AND orgid = '$orgid' ORDER BY login";
$result = $sqlcn->ExecuteSQL($sql)
		or die('Не могу выбрать список пользователей! ' . mysqli_error($sqlcn->idsqlconnection));
?>
<select name="suserid" id="suserid">
	<?php
	if ($addnone == 'true') {
		echo '<option value="-1">не выбрано</option>';
	}
	while ($row = mysqli_fetch_array($result)) {
		$z = $row['id'];
		$zx = new Tusers;
		$zx->GetById($z);
		$sl = ($z == $userid) ? 'selected' : '';
		echo "<option value=\"$z\" $sl>$zx->fio({$row['login']})</option>";
		unset($zx);
	}
	?>
</select>
<script>
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
</script>
