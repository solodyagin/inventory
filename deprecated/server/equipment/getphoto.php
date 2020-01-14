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

$eqid = GetDef('eqid');

$cfg = Config::getInstance();

$sql = 'SELECT * FROM equipment WHERE id = :eqid';
try {
	$row = DB::prepare($sql)->execute(array(':eqid' => $eqid))->fetch();
	$photo = ($row) ? $row['photo'] : '';
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список фото!', 0, $ex);
}
?>
<div class="thumbnail">
	<?php
	if ($photo != '') {
		echo '<img src="photos/' . $photo . '">';
	} else {
		echo '<img src="templates/' . $cfg->theme . '/img/noimage.jpg">';
	}
	?>
</div>
