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
$eqid = $req->get('eqid');
try {
	$sql = 'select * from equipment where id = :eqid';
	$row = db::prepare($sql)->execute([':eqid' => $eqid])->fetch();
	$photo = ($row) ? $row['photo'] : '';
} catch (PDOException $ex) {
	throw new dbexception('Не могу выбрать список фото!', 0, $ex);
}
?>
<div class="thumbnail">
	<?php
	if ($photo != '') {
		echo '<img src="photos/' . $photo . '">';
	} else {
		echo '<img src="public/img/noimage.jpg">';
	}
	?>
</div>
