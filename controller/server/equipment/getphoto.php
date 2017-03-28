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

$eqid = GetDef('eqid');

$photo = '';

$sql = 'SELECT * FROM equipment WHERE id = :eqid';
try {
	$row = DB::prepare($sql)->execute(array(':eqid' => $eqid))->fetch();
	if ($row) {
		$photo = $row['photo'];
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список фото!', 0, $ex);
}
?>
<div class="thumbnails">
    <a href="#" class="thumbnail">
		<?php
		if ($photo != '') {
			echo '<img src="/photos/' . $photo . '">';
		} else {
			echo '<img src="/templates/' . $cfg->theme . '/img/noimage.jpg">';
		}
		?>
	</a>
</div>
