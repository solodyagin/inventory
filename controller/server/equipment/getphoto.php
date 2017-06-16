<?php
/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

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
