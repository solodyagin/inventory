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

$crd = date('Y-m-d H:i:s');
$sql = <<<TXT
SELECT UNIX_TIMESTAMP('$crd') - UNIX_TIMESTAMP(lastdt) AS res,users_profile.fio AS fio,users_profile.jpegphoto
FROM   users
       INNER JOIN users_profile
               ON users_profile.usersid = users.id
TXT;
try {
	$arr = DB::prepare($sql)->execute()->fetchAll();
	foreach ($arr as $row) {
		$res = $row['res'];
		$fio = $row['fio'];
		$jpegphoto = $row['jpegphoto'];
		if ($res < 10000) {		
			if (!file_exists(WUO_ROOT . "/photos/$jpegphoto")) {
				$jpegphoto = 'noimage.jpg';
			}
			echo '<div class="col-sm-6 col-md-4">';
			echo '<div class="thumbnail">';
			echo '<img src="photos/' . $jpegphoto . '">';
			echo '<p align="center">' . $fio . '</p>';
			echo '</div>';
			echo '</div>';
		}
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список заходов пользователей!', 0, $ex);
}
